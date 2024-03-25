<div class="modal element-list fade reply_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" dir="ltr">
                <h5 class="modal-title mt-0" dir="<?= $GLOBALS['lang']['direction'] ?>"><span><?=__local('Reply To')?></span> <strong>Arman</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <?php 

                $extraClass = "confirm_and_reply";
                $extraInput = [
                    "<input type=\"hidden\" id=\"sub_action\" data-label=\"" .  __local("Sub Action") . "\" name=\"sub_action\" value=\"{$extraClass}\">",
                    "<input type=\"hidden\" id=\"post_type_id\" data-label=\"" .  __local("ID") . "\" name=\"post_type_id\" value=\"\">",
                    "<textarea id=\"content\" name=\"content\" data-label=\"" .  __local("Description") . "\" placeholder=\"" .  __local("Description") . "\" class=\"editor\"></textarea>",
                    "<div class=\"d-block text-center mt-3\">
                    <input type=\"submit\" value=\"" .  __local("Submit") . "\" class=\"btn btn-lg btn-primary\">
                </div>"
                ];
                
                require getDashboardViewPath("component/form/type.status.form.blade.php");
                ?>

                
            </div>
        </div>
    </div>
</div>