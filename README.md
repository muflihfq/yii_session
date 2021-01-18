# yii auth session

### Register
``` 
 [POST] /user/register
 email string
 password string
 name string
```
### Login
```
[POST] /user/login
email string
password string
```

### List Session
```
[GET] /session/list
```

### Detail Session
```
[GET] /session/detail?id={session_id}
```

### Create Session
```
[POST] /session/create
name string
description string
duration int
start datetime
```

### Update Session
```
[PUT] /session/update?id={session_id}
name string
description string
duration int
start datetime
```
### Delete Session
```
[PUT] /session/delete?id={session_id}
```
