let posts = [];

function escapeHtml(unsafeString) {
    var string = String(unsafeString);
    return string.replace(/[&<"'>]/g, (match) => {
        switch (match) {
            case '&':
                return '&amp;';
            case '<':
                return '&lt;';
            case '>':
                return '&gt;';
            case '"':
                return '&quot;';
            case "'":
                return '&#039;';
            default:
                return match;
        }
    });
}


function getTimeline(offset) {
    //get timeline
    $.get("get_timeline.php?offset=" + offset, function (response) {

        if (offset <= 0) {
            $("#posts").empty();
        }

        if (response.length > 0) {
            let post_id;
            let name;
            let surname;
            let content;
            let image;
            let profile_picture;

            for (let item of response) {
                post_id = item['post_id'];
                posts.push(post_id);

                name = escapeHtml(item['name']);
                surname = escapeHtml(item['surname']);
                content = escapeHtml(item['content']);
                image = escapeHtml(item['image']);
                profile_picture = escapeHtml(item['profile_picture']);

                let childElement = `<div class="post">
                <div class="friend" style="text-align:left;">
                    <img width="32" src="${profile_picture}"
                        style="margin-right: 5px">
                    <strong style="font-size: 20px">${name} ${surname}</strong>
                </div>
                <br>
                <p> ${content}
                <br>
                `;
                if (item['image']) {
                    childElement += `<br><img src="${image}" class="img-fluid">`
                }
                childElement += `</p>

                <div class="d-flex">
                    <div class="post-actions" name="post_${post_id}">
                <input type="hidden" value="${post_id}">`;
                if (item['i_liked'] != "0") {
                    childElement += `<button class="btn btn-light liked like-button">`;
                } else {
                    childElement += `<button class="btn btn-light like-button">`;
                }
                childElement += `<i class="bi bi-hand-thumbs-up"></i> Like
                            <span class="badge bg-info likeCount">${item['like_count']}</span>
                        </button>
                        <button class="btn btn-light comment-button">
                            <i class="bi bi-chat-dots"></i> Comment
                            <span class="badge bg-info commentCount">${item['comment_count']}</span>
                        </button>
                    </div>
                </div>
                <div class="comments">
                    <div name="getComments_${post_id}">
                    <div class="d-flex align-items-center">
                                <strong>Loading comments...</strong>
                                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
                            </div>
                    </div>
                    <input type="text" class="form-control" name="comment_content"  placeholder="Press enter to send your comment">
                </div>
            </div>`;


                $("#posts").append(childElement);

                let commentsField = $("div[name='getComments_" + post_id + "']");

                getComments(post_id, function (commentsData) {
                    if (commentsData.length > 0) {
                        commentsField.html(commentsData);
                    } else {
                        commentsField.html(`<br><b>There is no comment yet. Start a conversation now!</b><br><br>`)
                    }
                });
            }
            $("#posts").append("<center id='refreshField'><button class='btn btn-lg bg-primary text-white' id='refresh'><i class='bi bi-arrow-down'></i></button><br><br></center>");

        } else {
            $("#posts").append(`<center><b>You're all caught up!</b><br><br></center>`);
        }

        //like 
        $(".post-actions").on("click", ".btn", function () {
            if ($(this).hasClass("like-button")) {
                //like operations
                let postId = $(this).prev().val();
                let type;
                let postIdFormatted;
                let like_count;

                if ($(this).hasClass("liked")) {
                    type = "unlike";
                    $(this).removeClass("liked")
                } else {
                    type = "like";
                    $(this).addClass("liked")
                }

                postIdFormatted = "post_" + postId;
                like_count = document.getElementsByName(postIdFormatted)[0].getElementsByClassName("likeCount")[0];

                $.ajax({
                    url: 'like_post.php',
                    type: 'POST',
                    data: {
                        postId: postId,
                        type: type
                    },
                    success: function (response) {
                        console.log(response);
                        like_count.textContent = response["like_count"];

                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                        like_count.textContent = response["like_count"];

                    }
                });
            }


        });

        $("input[name='comment_content']").on("keypress", function (e) {
            if (e.which == 13) {
                let commentField = $(this);
                let postId = commentField.prev().attr('name').split("_")[1];
                let postIdFormatted;
                let comment_count;
                let content;

                postIdFormatted = "post_" + postId;
                comment_count = document.getElementsByName(postIdFormatted)[0].getElementsByClassName("commentCount")[0];

                content = commentField.val();

                if (content.length >= 3) {
                    $.ajax({
                        url: 'add_comment.php',
                        type: 'POST',
                        data: {
                            postId: postId,
                            comment_content: content
                        },
                        success: function (response) {
                            console.log(response);
                            comment_count.textContent = response["comment_count"];
                        },
                        error: function (xhr, status, error) {
                            console.log(error);
                            comment_count.textContent = response["comment_count"];

                        }
                    });

                    commentField.prop('disabled', true);

                    commentField.val("");

                    setTimeout(
                        function () {
                            getComments(postId, function (commentsData) {
                                let commentsField = $("div[name='getComments_" + postId + "']");
                                console.log(commentsData);
                                commentsField.html(commentsData);

                                commentField.prop('disabled', false);
                            });
                        }, 1000);


                } else {
                    confirm("Please type at least 3 characters.");
                }

            }
        });


        //refresh posts
        $("#refresh").on("click", function () {

            setCookie("offset", parseInt(getCookie("offset")) + 10);

            $("#refreshField").remove();
            getTimeline(getCookie("offset"));
        });


    });
}

function getComments(post_id, callback) {
    $.get("get_comments.php?post_id=" + post_id, function (response) {
        let returnData = "";

        if (response.length > 0) {

            let profile_picture;
            let name;
            let surname;
            let content;

            for (let commentItem of response) {
                profile_picture = escapeHtml(commentItem['profile_picture']);
                name = escapeHtml(commentItem['name']);
                surname = escapeHtml(commentItem['surname']);
                content = escapeHtml(commentItem['content']);

                returnData += `<div class="comment">
          <div class="friend" style="text-align:left; margin-bottom:8px;">
            <img width="32" src="${profile_picture}"
              style="margin-right: 5px">
            <strong>${name} ${surname}</strong>
          </div>
          <div>
            <p>${content}</p>
          </div>
        </div>`;
            }
        }

        callback(returnData); // Invoke the callback function with the data
    });
}


function getFriends() {
    //get friends
    $.get("get_friends.php", function (response) {
        $("#friend-list").empty();
        if (response.length > 0) {
            let profile_picture;
            let name;
            let surname;
            let user_id;

            for (let item of response) {
                profile_picture = escapeHtml(item['profile_picture']);
                name = escapeHtml(item['name']);
                surname = escapeHtml(item['surname']);
                user_id = escapeHtml(item['user_id']);

                let childElement = `<div class="friend row">
                <div class="col-3 avatar">
                    <img src="${profile_picture}">
                </div>
                <div class="full-name col-6">${name} ${surname}</div>
                <input type="hidden" name="friendId" value="${user_id}"> \
                <div class="col-2">
                    <button class="btn remove-friend"><i class="bi bi-person-x text-danger"
                            style="font-size: 24px;"></i></button>
                </div>
            </div>
            <hr>`;


                $("#friend-list").append(childElement);
            }
        } else {
            $("#friend-list").append(`<b>Unfortunately, you have no friends...</b>`);
        }
    });
}

function getNotifications() {
    $.get("get_notifications.php", function (response) {
        $("#notificationCount").text(response.length);
        $("#notifications").empty();

        let sender_profile_picture;
        let sender_name;
        let sender_surname;
        let sender_id;

        for (let item of response) {
            let childElement;

            sender_profile_picture = escapeHtml(item['sender_profile_picture']);
            sender_name = escapeHtml(item['sender_name']);
            sender_surname = escapeHtml(item['sender_surname']);
            sender_id = escapeHtml(item['sender_id']);

            if (item["type"] == "friend_request") {
                childElement = `<li> \
                <a class="dropdown-item" href="#">  \
                  <div class="d-flex align-items-center">  \
                    <img class="notification-avatar" src="${sender_profile_picture}">  \
                    <b> ${sender_name} ${sender_surname}&nbsp;</b> sent you a friend request. &nbsp; \
                    <input type="hidden" name="friendId" value="${sender_id}"> \
                    <div class="ml-auto friend-request-buttons">  \
                      <button class="btn btn-success btn-sm friend-request-accept" value="1">  \
                        <i class="bi bi-check"></i>  \
                      </button> \
                      <button class="btn btn-danger btn-sm friend-request-decline" value="0"> \
                        <i class="bi bi-x"></i> \
                      </button> \
                    </div> \
                  </div> \
                </a>  \
              </li> `;
            } else if (item["type"] == "friend_removed") {
                childElement = ` <li> \
                <a class="dropdown-item" href="#">  \
                  <div class="d-flex align-items-center">  \
                    <img class="notification-avatar" src="${sender_profile_picture}">  \
                    <b>${sender_name} ${sender_surname}&nbsp;</b> is no longer your friend. &nbsp; \
                  </div> \
                </a>  \
              </li>`;

            } else {

            }

            $("#notifications").append(childElement);
        }

        //accept friend request
        $(".friend-request-buttons").on("click", ".btn", function () {
            let friendId = $(this).parent().prev().val();
            let type;

            if ($(this).hasClass("friend-request-accept")) {
                type = "friend_accepted";
            } else {
                type = "friend_removed";
            }

            $.ajax({
                url: 'decide_friend_request.php',
                type: 'POST',
                data: {
                    friend_id: friendId,
                    type: type
                },
                success: function (response) {
                    console.log(response);
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });

            getFriends();
            getNotifications();

        })


    })
}
$(window).ready(async function () {
    // get friends
    getFriends();

    // get notifications
    getNotifications();

    // get posts
    setCookie("offset", 0);

    getTimeline(getCookie("offset"));

    //like 
    $(".post-actions").on("click", ".btn", function () {
        if ($(this).hasClass("like-button")) {
            //like operations
            let postId = $(this).prev().val();
            let type;
            let postIdFormatted;
            let like_count;

            if ($(this).hasClass("liked")) {
                type = "unlike";
                $(this).removeClass("liked")
            } else {
                type = "like";
                $(this).addClass("liked")
            }

            postIdFormatted = "post_" + postId;
            like_count = document.getElementsByName(postIdFormatted)[0].getElementsByClassName("likeCount")[0];

            $.ajax({
                url: 'like_post.php',
                type: 'POST',
                data: {
                    postId: postId,
                    type: type
                },
                success: function (response) {
                    console.log(response);
                    like_count.textContent = response["like_count"];

                },
                error: function (xhr, status, error) {
                    console.log(error);
                    like_count.textContent = response["like_count"];

                }
            });
        }


    });

});



//search
$("#search-input").change(function () {
    var inputValue = $(this).val();
    $("#search-results").empty();
    if (inputValue.length >= 2) {
        $.get("search_users.php?search=" + inputValue, function (response) {
            $("#search-results").empty();
            if (response.length > 0) {
                let profile_picture;
                let name;
                let surname;
                let user_id;

                for (let item of response) {
                    profile_picture = escapeHtml(item['profile_picture']);
                    name = escapeHtml(item['name']);
                    surname = escapeHtml(item['surname']);
                    user_id = escapeHtml(item['user_id']);

                    let childElement = `<div class="friend row"> \
                            <div class="col-3 avatar"> \
                              <img src="${profile_picture}" \
                                > \
                            </div> \
                            <div class="full-name col-6">${name} ${surname}</div> \
                            <input type="hidden" name="friendId" value="${user_id}"> \
                            <div class="col-2"> \
                              <button class="btn add-friend">`;

                    if (item["is_friend"] == 0) {
                        childElement += `<i class="bi bi-person-add text-primary" \
                                style="font-size: 24px"></i>`;

                    } else if (item["is_friend"] == 1) {
                        childElement += `<i class="bi bi-person-x text-danger" \
                                style="font-size: 24px"></i>`;
                    } else {
                        childElement += `<i class="bi bi-hourglass" \
                                style="font-size: 24px"></i>`;
                    }

                    childElement += `</button> \
                            </div> \
                          </div> \
                          <hr>`;

                    $("#search-results").append(childElement);
                }
            } else {
                $("#search-results").append(`<b>No results found...</b>`);
            }
        });
    } else {
        $("#search-results").append(`<b>Enter at least 2 characters to search...</b>`);
    }
});




$("#search-results").on("click", ".bi-person-add", function () {
    $(this).removeClass("bi-person-add text-primary");
    $(this).addClass("bi bi-hourglass");

    let friendId = $(this).parent().parent().prev().val();

    $.ajax({
        url: 'send_friend_request.php',
        type: 'POST',
        data: {
            friend_id: friendId
        }
    });
    getFriends();

})

$("#search-results").on("click", ".bi-person-x", function () {
    $(this).removeClass("bi-person-x text-danger");
    $(this).addClass("bi-person-add text-primary");

    let friendId = $(this).parent().parent().prev().val();

    $.ajax({
        url: 'remove_friend.php',
        type: 'POST',
        data: {
            friend_id: friendId
        }
    });
    getFriends();
});

//remove friend
$("#friend-list").on("click", ".bi-person-x", function () {
    $(this).removeClass("bi-person-x text-danger");
    $(this).addClass("bi-person-add text-primary");

    console.log($(this));
    let friendId = $(this).parent().parent().prev().val();

    $.ajax({
        url: 'remove_friend.php',
        type: 'POST',
        data: {
            friend_id: friendId
        }
    });

    getFriends();

});

//post image upload
$("#postImage").change(function () {
    if ($(this).value !== "") {
        $(this).prev().removeClass("btn-primary").addClass("btn-success");
        $(this).prev().empty().html('<i class="bi bi-image"></i>').append(" " + $(this).val().split("\\").pop());
    }
});



//get and set cookies taken from https://stackoverflow.com/questions/4825683/how-do-i-create-and-read-a-value-from-cookie-with-javascript
const setCookie = (name, value, days = 7, path = '/') => {
    const expires = new Date(Date.now() + days * 864e5).toUTCString()
    document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=' + path
}

const getCookie = (name) => {
    return document.cookie.split('; ').reduce((r, v) => {
        const parts = v.split('=')
        return parts[0] === name ? decodeURIComponent(parts[1]) : r
    }, '')
}

const deleteCookie = (name, path) => {
    setCookie(name, '', -1, path)
}
