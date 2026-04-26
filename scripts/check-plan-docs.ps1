$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

& node (Join-Path $PSScriptRoot 'check-plan-docs.js')
if ($LASTEXITCODE -ne 0) {
    throw 'Plan document checks failed.'
}
