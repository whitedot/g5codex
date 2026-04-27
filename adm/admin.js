// 관리자 JS 진입점.
// 공통 모듈을 초기화하고 submit token 주입만 연결한다. 화면별 상세 동작은 adm/admin-*.js를 확인한다.
document.addEventListener('DOMContentLoaded', function() {
    window.AdminShell.init();
    window.PopupManager.init();
    window.AdminConfigForm.init();
    window.AdminMemberList.init();
    window.AdminMemberExport.init();
    window.AdminMemberForm.init();
    document.addEventListener('click', function(event) {
        window.AdminSecurity.injectSubmitToken(event);
    });
});
