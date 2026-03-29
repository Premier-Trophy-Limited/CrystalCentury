param(
    [string]$RepoRoot = (Split-Path -Parent $PSScriptRoot)
)

$childTheme = Join-Path $RepoRoot 'child-theme'
$muPlugins  = Join-Path $RepoRoot 'mu-plugins'
$exports    = Join-Path $RepoRoot 'docs\exports'

New-Item -ItemType Directory -Force -Path $childTheme, $muPlugins, $exports | Out-Null

scp -r crystalcentury-user:public_html/wp-content/themes/hello-elementor-child/. $childTheme
scp crystalcentury-user:public_html/wp-content/mu-plugins/perf-tweaks.php (Join-Path $muPlugins 'perf-tweaks.php')
scp crystalcentury-user:public_html/wp-content/mu-plugins/arby-rollout-core.php (Join-Path $muPlugins 'arby-rollout-core.php')

$identity = ssh crystalcentury-user "bash -lc 'cd ~/public_html && wp --allow-root option get blogname && wp --allow-root option get home && wp --allow-root option get siteurl'"
$identity | Set-Content -Path (Join-Path $exports 'site-identity.txt')
