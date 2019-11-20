<?php
require_once __DIR__ . '/vendor/autoload.php';
//require './quickstart.php';

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

// Get the API client and construct the service object.
$client = getClient();
$range = 'Sheet1!A2:C4';
$service = new Google_Service_Sheets($client);

$spreadsheetId = '1yOnaXfyhLbDN4fM8THZyIxkeMIOeBzp9zs9oVrUWHc0';
$result = $service->spreadsheets_values->get($spreadsheetId, $range);
$numRows = $result->getValues() != null ? count($result->getValues()) : 0;
//$result is an object
//echo '<pre>' ;  print_r((array)$result); echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8" name="Sakura bentos" content="sakura bento restaurant chain japanese cooking cuisine">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link href="https://github.com/0ctavia/restaurant/blob/master/assets/images/flower.ico?raw=true" rel="icon" type="image/x-icon" />
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans&display=swap" rel="stylesheeth">
        <link href="https://fonts.googleapis.com/css?family=Chilanka&display=swap" rel="stylesheet">

    <title>Guestbook Sakura bento</title>
</head>
<body>
<h1>Guestbook</h1>
 
<table id="guestbookTable">

<tr>
    <th>Nom</th>
    <th>Date</th>
    <th>Commentaire</th>
</tr>
<?php
foreach($result['values'] as $element){
        echo '<tr>';
        foreach($element as $item){
            echo '<td>'. $item.'</td>';
        }
        echo '</tr>';
    }
?>
</table>
</body>
</html>