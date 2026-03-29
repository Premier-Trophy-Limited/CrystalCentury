param(
    [string]$BaseUrl = 'https://www.crystalcentury.com'
)

$ErrorActionPreference = 'Stop'

$repoRoot   = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$auditsDir  = Join-Path $repoRoot 'docs\audits'
$exportsDir = Join-Path $repoRoot 'docs\exports'
$utf8NoBom  = New-Object System.Text.UTF8Encoding($false)
$rootUrl    = $BaseUrl.TrimEnd('/')
$pages      = @(
    @{ Name = 'home'; Path = '/'; File = 'live-home.html' },
    @{ Name = 'shop'; Path = '/shop/'; File = 'live-shop.html' }
)

New-Item -ItemType Directory -Path $auditsDir -Force | Out-Null
New-Item -ItemType Directory -Path $exportsDir -Force | Out-Null

$generatedAtUtc = [DateTime]::UtcNow.ToString('o')
$cartLinkRegex  = [regex]::Escape($rootUrl) + '/cart/[^"]+" class="woocommerce-LoopProduct-link'
$legalLinkRegex = '/legal/(privacy-policy|terms-and-conditions|refund-and-returns|shipping-and-delivery)\.html'

$results = foreach ($page in $pages) {
    $uri        = '{0}{1}' -f $rootUrl, $page.Path
    $outputPath = Join-Path $auditsDir $page.File
    $response   = Invoke-WebRequest -Uri $uri -MaximumRedirection 5 -UseBasicParsing

    [System.IO.File]::WriteAllText($outputPath, $response.Content, $utf8NoBom)

    [pscustomobject]@{
        name                    = $page.Name
        sourceUrl               = $uri
        outputPath              = $outputPath
        fetchedAtUtc            = $generatedAtUtc
        statusCode              = [int]$response.StatusCode
        cartBaseProductLinkCount = ([regex]::Matches($response.Content, $cartLinkRegex)).Count
        enquiryButtonCount      = ([regex]::Matches($response.Content, 'cc-enquiry-btn')).Count
        footerQuoteCtaCount     = ([regex]::Matches($response.Content, 'data-arby-track="footer_request_quote"')).Count
        hasWpmlDevBanner        = ($response.Content -match 'otgs-development-site-front-end')
        legalLinkCount          = ([regex]::Matches($response.Content, $legalLinkRegex)).Count
    }
}

$summary = [pscustomobject]@{
    baseUrl        = $rootUrl
    generatedAtUtc = $generatedAtUtc
    pages          = $results
}

$summaryPath = Join-Path $exportsDir 'live-audit-summary.json'
$summaryJson = $summary | ConvertTo-Json -Depth 5
[System.IO.File]::WriteAllText($summaryPath, $summaryJson + [Environment]::NewLine, $utf8NoBom)

$results |
    Select-Object name, statusCode, cartBaseProductLinkCount, enquiryButtonCount, footerQuoteCtaCount, hasWpmlDevBanner |
    Format-Table -AutoSize

Write-Host ('Saved live HTML captures to {0}' -f $auditsDir)
Write-Host ('Saved audit summary to {0}' -f $summaryPath)
