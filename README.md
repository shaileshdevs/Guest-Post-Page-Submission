# Guest Post Page Subsmission WordPress Plugin

There are two shortcodes in this plugin.

1. [gpps_post_submission_form]
2. [gpps_post_pending_list]

**[gpps_post_submission_form]**
- This shortcode displays a form to be filled by the non-logged in user (guest user).
- The form is not visible to any logged in user.
- When a non-logged in user (guest user) submits the form, it creates a post with the post_type as 'post'.
- It attaches the uploaded image with the created post.
- The post_status for the newly created post is set to 'pending' for an admin approval.
- An admin receives the email whenever any non-logged in user (guest user) creates a post through this form.
- An admin also receives the post edit link in the email itself for easy navigation.
- The _Post Title_ field in the form is mandatory.
- It also validates the uploaded file and only allows the image file to be uploaded.
- It attaches the uploaded image file with the newly created post.

**[gpps_post_pending_list]**
- This shortcode displays a list of posts with the pending status for an admin approval.
- It displays the five columns: ID (post id), Title (post title), Excerpt (post excerpt), Edit Link (post edit link), Action (button to approve the post)
- It also displays the pagination to properly navigate among the list of posts.
- The list is only visible to an administrator. It is not visible to any other logged in user and non-logged in user.
- When an admin clicks on the _'Approve'_ button of the particular post, the post status gets changed to the 'Publish' status.
- The '_Approve'_ button of that particular post gets disabled and its text is changed to 'Approved'.
- If an admin reloads the page, that particular post is not visible in the list, because it is no more in the pending status.
