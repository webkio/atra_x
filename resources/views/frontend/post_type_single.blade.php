@include("frontend.parts.header")

    {!!getPostTypeThumbnail($DB , "x" , "" , "")!!}
    <h1>{{getTypeTitle($DB)}}</h1>
    <div class="content">
        {!!getTypeBody($DB)!!}
    </div>

    <div class="text-center">
        {!!getPostTypeTaxonomyLinks($DB , [])!!}
    </div>
    {!!showMenuFrontEnd("menu-1")!!}

    <b>by</b> {{getPostTypeAuthor($DB , "create" , "fullname")}}

    <div class="comments bg-primary text-white">
        <div class="comment-form-wrapper">
            <?php
            echo getCommentForm($DB, 0, "comment");
            ?>
        </div>

        <div class="comment-list-wrapper mt-5">
            <ul>
                <?php
                $comments = get_post_type_comments($DB, 0, null, "comment", null, true);
                if ($comments && empty($comments['error'])) {
                    echo $comments['comment_list'];
                    echo $comments['pagination'];
                } else if (!empty($comments['error'])) {
                    echo $comments['error'];
                }

                ?>

            </ul>
        </div>

    </div>
@include("frontend.parts.footer")