# Documentation of GlobeMail API

## User login:

`-X POST -F "access_token=[token]" /user/login `

Here, token is the access token which you require from Google sign in API usingi JavaScript.

If successful, response will be a JSON data with access token

Note: If data is not in correct format, it will throw error with error message.

