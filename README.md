# Documentation of GlobeMail API

## User login:

`-X POST -F "access_token=[token]" /user/login`

Here, token is the access token which you require from Google sign in API using JavaScript.

If successful, response will be a JSON data with access token

Note: If data is not in correct format, it will throw error with error message.

# Trackers

Trackers are those trackers which are sent via emails and help us notify user if the email is read.

### Generating a tracker
`-X POST -H "auth-token: [token]" "/delivery/image/"`

Should post alias for that tracker
Response will contain the tracker just created sample response:

`{"status":"Success","image":"9fef91e30bc272a6dabf88cb4c28d5b4586b9c9fb4cd0a85e99cf5d2a2d9d8f1.jpg"}`

### Listing trackers

`-X GET -H "auth-token: [token]" "/delivery/image/`

Response will contain list of tracker current user has created

### Deleting trackers

It's a good idea to delete trackers if user no longer wants that, on Front End, we should show list of trackers and give them an option to delete them.

Sample request to delete a tracker would look like:

```
-X DELETE -H "auth-token: 9dc1dc492775c7aca1582d3cb6bcf5f2"
-H "Content-Type: application/x-www-form-urlencoded"
-d 'image=934e637915d7e2096cf896c090a39272cc64ace916e073f6fce58f6bf38c46c7.jpg'
"/delivery/image/"
```
And sample response would look like:
```
{
  "status": "Success",
  "message": "Image deleted"
}
```
