Betable OAuth PHP SDK
=====================

Before bets may be placed through the Betable API, the client must be authorized by the player using OAuth.

The Betable OAuth PHP SDK (like the other Betable OAuth SDKs) redirects the player to <https://betable.com/authorize> and when they return, completes the OAuth protocol to produce an access token.

Requirements
------------

PHP 5.

Usage
-----

**Configure the Betable OAuth PHP SDK**:

    $betable = new Betable(
        "YOUR_CLIENT_ID",
        "YOUR_CLIENT_SECRET",
        "YOUR_REDIRECT_URI"
    );

**Redirect the player to Betable**:

    $betable->authorize();

**When the player is redirected back to your redirect URI, complete the OAuth protocol to produce an access token**:

    $access_token = $betable->token();

If you call `token` at an inappropriate time, it will return `false`.  This can be useful in detecting where the player is in the OAuth protocol.

**Configure the [Betable Browser SDK](https://github.com/betable/betable-browser-sdk)**:

    <script src="betable-browser-sdk.js"></script>
    <script>
    var accessToken = '<?= $access_token ?>';
    </script>

**Get the player's account, which includes their first and last name**:

    $response = $betable->account();
    #
    # response:
    #
    #     {
    #         "id": "A4n7V5UL3gKx8ms2"
    #       , "first_name": "Charles"
    #       , "last_name": "Fey"
    #     }
    #

**Get the player's wallet, which includes their real-money balance**:

    $response = $betable->wallet();
    #
    # response:
    #
    #     {
    #       , "real": {
    #             "balance": "0.00"
    #           , "currency": "GBP"
    #           , "economy": "real"
    #         }
    #       , "sandbox": {
    #             "balance": "0.00"
    #           , "currency": "GBP"
    #           , "economy": "sandbox"
    #         }
    #     }
    #

Full documentation may be found at <https://developers.betable.com/docs/>.

Example
-------

<https://github.com/betable/betable-oauth-php-sdk/blob/master/index.php>
