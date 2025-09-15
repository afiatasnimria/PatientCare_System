$ErrorActionPreference = 'Stop'
$sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession

Write-Output "--- Register ---"
$regBody = @{ role='patient'; name='E2E Test'; username='e2e_test@example.com'; password='TestPass123!'; phone='012345' }
$reg = Invoke-RestMethod -Uri 'http://localhost:8000/register_user.php' -Method Post -Body (ConvertTo-Json $regBody) -ContentType 'application/json' -WebSession $sess
$reg | ConvertTo-Json

Write-Output "--- Authenticate ---"
$authBody = @{ role='patient'; username='e2e_test@example.com'; password='TestPass123!' }
$auth = Invoke-RestMethod -Uri 'http://localhost:8000/authenticate.php' -Method Post -Body (ConvertTo-Json $authBody) -ContentType 'application/json' -WebSession $sess
$auth | ConvertTo-Json

Write-Output "--- Upload photo ---"
$form = @{ photo = Get-Item 'D:\e2e_test_img.png' }
$up = Invoke-RestMethod -Uri 'http://localhost:8000/upload_profile_photo.php' -Method Post -Form $form -WebSession $sess
$up | ConvertTo-Json

Write-Output "--- Save profile ---"
$saveBody = @{ full_name='E2E Test Updated'; address='42 Test St'; house_no='H-9'; city='Testville'; postal_code='9999'; blood_group='A+'; last_donation='2025-09-01'; donation_count=2; phone='012345'; email='e2e_test@example.com'; emergency_contact='EC (+880)'; photo = $up.url }
$save = Invoke-RestMethod -Uri 'http://localhost:8000/save_profile.php' -Method Post -Body (ConvertTo-Json $saveBody) -ContentType 'application/json' -WebSession $sess
$save | ConvertTo-Json

Write-Output "--- Fetch profile.php ---"
$html = Invoke-RestMethod -Uri 'http://localhost:8000/profile.php' -WebSession $sess -UseBasicParsing
# Print a short preview of the returned HTML
$content = $html.Content
$preview = $content.Substring(0,[Math]::Min(1200,$content.Length))
$preview
