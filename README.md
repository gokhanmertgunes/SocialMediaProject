# 256 Project 

## DB

```
Table: Users
Columns:
user_id (Primary Key, Integer, Auto Increment)
name (String, Maximum length: 50)
surname (String, Maximum length: 50)
email (String, Maximum length: 100)
password (String, Maximum length: 255) [Stored as hashed password]
birth_date (Date)
profile_picture (String, Maximum length: 255)


Table: Posts
Columns:
post_id (Primary Key, Integer, Auto Increment)
user_id (Foreign Key referencing Users table)
content (Text)
image (String, Maximum length: 255)
timestamp (DateTime)

Table: Friends
Columns:
user_id (Foreign Key referencing Users table)
friend_id (Foreign Key referencing Users table)
status (String, Maximum length: 10) [Values: 'pending', 'accepted', 'rejected']

Table: Comments
Columns:
comment_id (Primary Key, Integer, Auto Increment)
post_id (Foreign Key referencing Posts table)
user_id (Foreign Key referencing Users table)
content (Text)
timestamp (DateTime)

Table: Likes
Columns:
like_id (Primary Key, Integer, Auto Increment)
post_id (Foreign Key referencing Posts table)
user_id (Foreign Key referencing Users table)
timestamp (DateTime)

Table: Notifications
Columns:
notification_id (Primary Key, Integer, Auto Increment)
user_id (Foreign Key referencing Users table)
sender_id (Foreign Key referencing Users table)
type (String, Maximum length: 20) [Values: 'friend_request', 'friend_accepted', 'friend_removed']
timestamp (DateTime)
```


## Tree

```
- index.php
- assets/
  - css/
    - style.css
  - js/
    - main.js
  - images/
    - (various images)
- templates/
  - header.php
  - footer.php
  - home.php
  - login.php
  - register.php
  - profile.php
  - timeline.php
  - friends.php
  - notifications.php
  - search.php
- includes/
  - db.php
  - auth.php
  - functions.php
  - validation.php
  - security.php
- scripts/
  - register.php
  - login.php
  - logout.php
  - add_post.php
  - retrieve_posts.php
  - add_comment.php
  - like_unlike_post.php
  - search_friends.php
  - send_friend_request.php
  - accept_friend_request.php
  - remove_friend.php
- uploads/
  - profile_pictures/
    - (user-specific profile pictures)
- error/
  - 404.php
  - 500.php
```


## phpMyAdmin Credentials:

**URL:**
https://databases.000webhost.com/db_sql.php?db=id20787719_socialmedia_db

**Password:**
CTIS@256project
