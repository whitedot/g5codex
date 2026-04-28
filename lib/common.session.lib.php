<?php
if (!defined('_GNUBOARD_')) exit;

// 세션변수 생성
function set_session($session_name, $value)
{
    global $g5;

    static $check_cookie = null;
    
    if( $check_cookie === null ){
        $cookie_session_name = session_name();
        if( ! isset($g5['session_cookie_samesite']) && ! ($cookie_session_name && isset($_COOKIE[$cookie_session_name]) && $_COOKIE[$cookie_session_name]) && ! headers_sent() ){
            @session_regenerate_id(false);
        }

        $check_cookie = 1;
    }

    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // PHP 버전별 차이를 없애기 위한 방법
    $$session_name = $_SESSION[$session_name] = $value;
}

// 세션변수값 얻음
function get_session($session_name)
{
    return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}

// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire, $path='/', $domain=G5_COOKIE_DOMAIN, $secure=false, $httponly=true)
{
    global $g5;
    
    $c = run_replace('set_cookie_params', array('path'=>$path, 'domain'=>$domain, 'secure'=>$secure, 'httponly'=>$httponly), $cookie_name);
    
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $c['secure'] = true;
    }

    setcookie(md5($cookie_name), base64_encode($value), G5_SERVER_TIME + $expire, $c['path'], $c['domain'], $c['secure'], $c['httponly']);
}

// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
    $cookie = md5($cookie_name);
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}

// 토큰 생성
function _token()
{
    return g5_generate_hex_token(16);
}

// 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
function get_token()
{
    $token = _token();
    set_session('ss_token', $token);

    return $token;
}

// POST로 넘어온 토큰과 세션에 저장된 토큰 비교
function check_token()
{
    $token = get_session('ss_token');
    $request_token = '';

    if (isset($_POST['token']) && !is_array($_POST['token'])) {
        $request_token = (string) $_POST['token'];
    } elseif (isset($_REQUEST['token']) && !is_array($_REQUEST['token'])) {
        $request_token = (string) $_REQUEST['token'];
    }

    set_session('ss_token', '');

    return $token !== '' && $request_token !== '' && g5_hash_equals($token, $request_token);
}

/**
 * 브라우저 검증을 위한 세션 반환 및 재생성
 * @param array $member 로그인 된 회원의 정보. 가입일시(mb_datetime)를 반드시 포함해야 한다.
 * @param bool $regenerate true 이면 재생성
 * @return string
 */
function ss_mb_key($member, $regenerate = false)
{
    $client_key = ($regenerate) ? null : get_cookie('mb_client_key');

    if (!$client_key) {
        $client_key = get_random_token_string(16);
        set_cookie('mb_client_key', $client_key, G5_SERVER_TIME * -1);
    }

    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $user_agent_key = run_replace('ss_mb_key_user_agent', $user_agent);
    $mb_key = g5_build_hmac_token('member-session', $member['mb_datetime'] . '|' . $client_key . '|' . $user_agent_key);

    return $mb_key;
}

/**
 * 회원의 클라이언트 검증
 * @param array $member 로그인 된 회원의 정보. 가입일시(mb_datetime)를 반드시 포함해야 한다.
 * @return bool
 */
function verify_mb_key($member)
{
    $mb_key = ss_mb_key($member);
    $verified = g5_hash_equals($mb_key, get_session('ss_mb_key'));

    if (!$verified) {
        ss_mb_key($member, true);
    }

    return $verified;
}

/**
 * 회원의 클라이언트 검증 키 생성
 * 클라이언트 키를 다시 생성하여 생성된 키는 `ss_mb_key` 세션에 저장됨
 * @param array $member 로그인 된 회원의 정보. 가입일시(mb_datetime)를 반드시 포함해야 한다.
 */
function generate_mb_key($member)
{
    $mb_key = ss_mb_key($member, true);
    set_session('ss_mb_key', $mb_key);
}

function check_auth_session_token($str=''){
    if (g5_hash_equals(get_token_encryption_key($str), get_session('ss_mb_token_key'))) {
        return true;
    }
    return false;
}

function update_auth_session_token($str=''){
    set_session('ss_mb_token_key', get_token_encryption_key($str));
}

function get_token_encryption_key($str=''){
    return g5_build_hmac_token('auth-session', G5_TABLE_PREFIX . '|' . $str);
}

function g5_build_auto_login_key($member_password)
{
    $server_addr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
    $server_software = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    return g5_build_hmac_token('auto-login', $server_addr . '|' . $server_software . '|' . $user_agent . '|' . $member_password);
}

function g5_build_legacy_auto_login_key($member_password)
{
    $server_addr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
    $server_software = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    return md5($server_addr . $server_software . $user_agent . $member_password);
}

function g5_build_register_email_key($member_ip, $member_datetime)
{
    return g5_build_hmac_token('register-email', $member_ip . '|' . $member_datetime);
}

function g5_get_token_secret()
{
    if (defined('G5_TOKEN_ENCRYPTION_KEY') && G5_TOKEN_ENCRYPTION_KEY !== '') {
        return G5_TOKEN_ENCRYPTION_KEY;
    }

    $parts = array(
        defined('G5_MYSQL_PASSWORD') ? G5_MYSQL_PASSWORD : '',
        defined('G5_MYSQL_DB') ? G5_MYSQL_DB : '',
        defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : '',
        defined('G5_PATH') ? G5_PATH : '',
    );

    return implode('|', $parts);
}

function g5_build_hmac_token($purpose, $value)
{
    return hash_hmac('sha256', (string) $purpose . '|' . (string) $value, g5_get_token_secret());
}

function get_random_token_string($length=6)
{
    return g5_generate_hex_token($length);
}

function g5_secure_random_bytes($bytes)
{
    $bytes = (int) $bytes;
    if ($bytes < 1) {
        $bytes = 16;
    }

    if (function_exists('random_bytes')) {
        return random_bytes($bytes);
    }

    if (function_exists('openssl_random_pseudo_bytes')) {
        $strong = false;
        $random = openssl_random_pseudo_bytes($bytes, $strong);
        if ($random !== false && $strong === true && strlen($random) === $bytes) {
            return $random;
        }
    }

    if (function_exists('mcrypt_create_iv') && defined('MCRYPT_DEV_URANDOM')) {
        $random = mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
        if ($random !== false && strlen($random) === $bytes) {
            return $random;
        }
    }

    throw new RuntimeException('보안 난수를 생성할 수 없습니다.');
}

function g5_generate_hex_token($bytes = 16)
{
    $bytes = (int) $bytes;
    if ($bytes < 1) {
        $bytes = 16;
    }

    return bin2hex(g5_secure_random_bytes($bytes));
}

function g5_secure_random_int($min, $max)
{
    $min = (int) $min;
    $max = (int) $max;

    if (function_exists('random_int')) {
        return random_int($min, $max);
    }

    if ($min > $max) {
        throw new InvalidArgumentException('최소값은 최대값보다 작거나 같아야 합니다.');
    }

    $range = $max - $min;
    if ($range === 0) {
        return $min;
    }

    $bytes = (int) ceil(log($range + 1, 2) / 8);
    $mask = (1 << (int) ceil(log($range + 1, 2))) - 1;

    do {
        $random = 0;
        $random_bytes = g5_secure_random_bytes($bytes);
        for ($i = 0; $i < $bytes; $i++) {
            $random = ($random << 8) | ord($random_bytes[$i]);
        }
        $random = $random & $mask;
    } while ($random > $range);

    return $min + $random;
}

function g5_hash_equals($known_string, $user_string)
{
    $known_string = (string) $known_string;
    $user_string = (string) $user_string;

    if (function_exists('hash_equals')) {
        return hash_equals($known_string, $user_string);
    }

    if (strlen($known_string) !== strlen($user_string)) {
        return false;
    }

    $result = 0;
    $length = strlen($known_string);
    for ($i = 0; $i < $length; $i++) {
        $result |= ord($known_string[$i]) ^ ord($user_string[$i]);
    }

    return $result === 0;
}
