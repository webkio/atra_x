<?php

namespace App\Http\Controllers;

use App\Models\OTP;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {

        $levels = getCurrentUserAccessLevelEntity_Create();
        $status_s = getCurrentUserStatusEntity_Create(null, "simple");

        // make it global for accessing title and other data
        generateGlobalTitle(new User);

        return view("dashboard.user_create", [
            'action' => __FUNCTION__,
            'the_ID' => false,
            'DB' => [],
            'levels' => $levels,
            'status_s' => $status_s,
            'route_args' => []
        ]);
    }

    public function store()
    {
        


        $current_user = getCurrentUser();
        $inputs = cleanTheArray(request()->post(), false);

        // lowercase
        $inputs = makeTheseItemsLowercase($inputs, ['email', 'username']);

        // change required fields from default to phone if `account_type_registeriation` differnet from `login`
        $account_type_registeriation = getAllType("account_type_registeriation");

        // add require inputs
        addRequireInputBasedOnLoginType($account_type_registeriation, __FUNCTION__, $inputs);


        $inputs['username'] = $inputs['username'] ?? "";
        $inputs['username'] = strtolower($inputs['username']);


        // limitation for description
        if (isset($inputs['description']) && getUserMaxDescriptionLength() < mb_strlen($inputs['description'])) {
            $inputs['description'] = mb_substr($inputs['description'], 0, getUserMaxDescriptionLength());
        }


        if (!$current_user) {
            


            $inputs['role'] = $inputs['choosable_role'];
            $inputs['status'] = "deactive";

            // check captcha
            if (!isCaptchaDisabled("user", "sign_up")) {
                if (!validateCaptcha()) {
                    return triggerServerError(getUserMessageValidate(getMessageCaptchaInvalid(), []));
                }
            }
        }

        do_action("before_user_" . __FUNCTION__ . "_check", $inputs);

        $levels = getUserRoles();
        $status_s = getStatusUser();

        if (auth()->check()) {
            $levels = getCurrentUserAccessLevelEntity_Create();

            if (!$levels) {
                return triggerServerError(getUserMessageValidate(getMessageUserNotHavePermissionToAddUser(), []));
            }

            $status_s = getCurrentUserStatusEntity_Create(null, "simple");
        }

        $error = validationUserFormInputs($inputs, [
            "status" => $status_s,
            "role" => $levels
        ], [new User(), [$account_type_registeriation, "username"]]);

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_user_" . __FUNCTION__ . "_check", $inputs);

        // create user
        $new_user = User::create([
            "username" => $inputs['username'],
            "email" => $inputs['email'],
            "phone" => $inputs['phone'],
            "fullname" => encodeEmojiCharactersToHtml($inputs['fullname']),
            "password" => bcrypt($inputs['password']),
            "role" => $inputs['role'],
            "description" => encodeEmojiCharactersToHtml(@$inputs['description']),
            "status" => $inputs['status']
        ]);

        $new_user->save();
        $new_user->refresh();

        checkClientIDByStatus($inputs, $new_user);

        do_action("user_successfully_" . __FUNCTION__ . "d", $new_user, $inputs);

        if ($current_user) {
            $redirect = getTypeEditLink($new_user, "user", ["id"]);
        } else {
            $response = sendOTPToClient($new_user, "sign_up", "verify_{$account_type_registeriation}", $account_type_registeriation);
            if (is_a($response, 'Illuminate\Http\RedirectResponse')) {
                return $response;
            } else if (is_string($response)) {
                $redirect = $response;
            }
        }

        return redirect($redirect);
    }

    public function edit($id)
    {
        $user = User::where("id", $id)->get();
        $user = $user->first();

        abortByEntity($user);
        abortByRole($user, $id);

        $levels = getCurrentUserAccessLevelEntity_Create($id);
        $status_s = getCurrentUserStatusEntity_Create($id, "dynamic");

        $user['password'] = "";

        // make it global for accessing title and other data
        generateGlobalTitle($user);

        do_action("user_edit_action", $user);

        return view("dashboard.user_create", [
            'action' => __FUNCTION__,
            'DB' => $user,
            'levels' => $levels,
            'status_s' => $status_s,
            'the_ID' => $id,
            'can_delete' => canUserAction($user, "delete"),
            'route_args' => [
                "id" => $id
            ]
        ]);
    }

    public function update($id)
    {
        $inputs = cleanTheArray(request()->post(), false);

        $account_type_registeriation = getAllType("account_type_registeriation");

        // user
        $user = User::findOrFail($id);

        // lowercase
        $inputs = makeTheseItemsLowercase($inputs, ['email', 'username']);

        if ($account_type_registeriation != "email") {
            $inputs['email'] = getTypeEmail($user);
        }

        $inputs['username'] = $inputs['username'] ?? "";
        $inputs['username'] = strtolower($inputs['username']);

        if (!isset($inputs['password']) || @$inputs['password'] == "") {
            $password_regex = typeRegexData("password_user");
            $inputs['password'] = $password_regex['sample'];
        }

        // limitation for description
        if (isset($inputs['description']) && getUserMaxDescriptionLength() < mb_strlen($inputs['description'])) {
            $inputs['description'] = mb_substr($inputs['description'], 0, getUserMaxDescriptionLength());
        }

        // add require inputs
        addRequireInputBasedOnLoginType($account_type_registeriation, __FUNCTION__, $inputs, $user);

        do_action("before_user_" . __FUNCTION__ . "_check", $inputs);

        $levels = getCurrentUserAccessLevelEntity_Create($id);
        $status_s = getCurrentUserStatusEntity_Create($id, "dynamic");

        $allowedData = [
            "status" => $status_s,
            "role" => $levels
        ];

        $existsDataFields = ["email", "username"];
        if ($account_type_registeriation != "email") {
            $existsDataFields[] = $account_type_registeriation;
        }
        $existsData = [new User(), $existsDataFields, $id];

        $error = validationUserFormInputs($inputs, $allowedData, $existsData);


        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_user_" . __FUNCTION__ . "_check", $inputs);

        $old_status = $user['status'];

        // check password for updating
        $password = !empty($inputs['password']) && !isset($password_regex) ? bcrypt($inputs['password']) : getTypePassword($user);


        $user->update([
            "username" => $inputs['username'],
            "fullname" => encodeEmojiCharactersToHtml($inputs['fullname']),
            "password" => $password,
            "role" => $inputs['role'],
            "description" => encodeEmojiCharactersToHtml(@$inputs['description']),
            "theme_color" => isset($inputs['theme_color']) ? $inputs['theme_color'] : $user['theme_color'],
            "theme_color_hover" => isset($inputs['theme_color_hover']) ? $inputs['theme_color_hover'] : $user['theme_color_hover'],
            "status" => $inputs['status']
        ]);

        checkClientIDByStatus($inputs, $user, $old_status);

        do_action("user_successfully_" . __FUNCTION__ . "d", $user, $inputs);
        do_action("user_" . $inputs['role'] . "_successfully_" . __FUNCTION__ . "d", $user, $inputs);

        return redirect(getTypeEditLink($user, "user", ["id"]))->withErrors(restMessageEncode([
            "message" => __local(getActionTypeDataByInterface("updated")['message']),
            "data" => [],
        ]));
    }

    public function index()
    {
        do_action("user.list");

        $levels = getCurrentUserAccessLevelEntity_Create();

        $user = User::whereIn("role", array_keys($levels));

        // make it global for accessing title and other data
        generateGlobalTitle(new User);

        $filterHtml = getTableHeadUser();

        $user = filterListHandler($user, $filterHtml);

        return view("dashboard.user_list", [
            'DB' => $user,
            'route_args' => []
        ]);
    }

    public function destroy()
    {
        return deleteType(getFullNamespaceByModel("User", "findOrfail"), ["callback" => function () {
            $data = getModelAndInputsByID("User");

            // check user permission for action delete
            $delete_res = checkUserDeletePermission($data['model']);
            if (is_object($delete_res)) {
                return $delete_res;
            }

            return true;
        }, "callback_args" => []], "fullname");
    }

    public function statusType()
    {
        $listStatus = getStatusUser("*");

        $targetStatus = $listStatus[request()->post("action", "")] ?? "";
        $cbk = $targetStatus ? $targetStatus['onAction'] : null;

        $result = setStatusType("User", "user", [
            "callback" => $cbk,
            "callback_args" => [],
        ], "fullname");
        return $result;
    }

    // =========> public features

    public function pub_signup()
    {

        // make it global for accessing title and other data
        generateGlobalTitle(new User);

        return view("dashboard.user_panel.sign_up", [
            'DB' => [],
            'route_args' => []
        ]);
    }

    public function pub_signin()
    {

        // make it global for accessing title and other data
        generateGlobalTitle(new User);

        return view("dashboard.user_panel.sign_in", [
            'DB' => [],
            'route_args' => []
        ]);
    }

    public function pub_signin_action()
    {
        $inputs = request()->post();

        $account_type_registeriation = getAllType("account_type_registeriation");

        do_action("before_signin_check", $inputs);

        // check captcha
        if (!isCaptchaDisabled("user", "sign_in")) {
            if (!validateCaptcha()) {
                return triggerServerError(getUserMessageValidate(getMessageCaptchaInvalid(), []));
            }
        }

        $error = validationUserFormInputs($inputs, [], []);
        if ($error['message']) {
            return triggerServerError($error);
        }

        $user  = new \App\Models\User;

        $client_IDs = getUserClientIDList();

        foreach ($client_IDs as $client_ID) {
            $user = $user->orWhere($client_ID, $inputs['client_id']);
        }

        $user = $user->first();

        $checkUserPassword = Hash::check($inputs['password'] ?? "", getTypePassword($user));

        if (!(isset($user) && $checkUserPassword)) {
            if ($account_type_registeriation == "email") {
                $user = null;
            }
        } else {
            // check user is blocked
            $checkBlocked = checkUserBlocked($user, "back");
            if (is_object($checkBlocked)) return $checkBlocked;
        }

        $response = null;
        $redirect = null;
        if (!$user) {
            $response = triggerServerError(getUserMessageValidate(__local("one of these fields (x-field) or all are Wrong"), array_keys(add_require_sign_in_fields())));
        } else if ($user) {
            if ($account_type_registeriation == "email") {
                auth()->login($user, isset($inputs['remember_me']));
                $redirect = getTypeEditLink($user, "user", ["id"]);
            } else if ($account_type_registeriation == "phone") {
                // send sms
                $response = sendOTPToClient($user, "sign_in", "verify_{$account_type_registeriation}", $account_type_registeriation);

                if (is_string($response)) {
                    $redirect = $response;
                }
            }
        }

        if (is_a($response, 'Illuminate\Http\RedirectResponse')) {
            return $response;
        }


        return redirect($redirect);
    }



    public function pub_reset_password($via = null)
    {

        $via = $via ??  getAllType('account_type_registeriation');

        // redirect if not allowed 
        $redirect = redirectFromToB($via != "email", route("user.signin.form"));
        if ($redirect) return $redirect;

        // make it global for accessing title and other data
        generateGlobalTitle(new User);

        return view("dashboard.user_panel.reset_password", [
            'DB' => [],
            'route_args' => [
                'via' => $via
            ]
        ]);
    }

    public function pub_reset_password_action($via = null)
    {
        $via = $via ?? getAllType('account_type_registeriation');

        // redirect if not allowed 
        $redirect = redirectFromToB($via != "email", route("user.signin.form"));
        if ($redirect) return $redirect;

        $inputs = request()->post();

        // check captcha
        if (!isCaptchaDisabled("user", "reset_password")) {
            if (!validateCaptcha()) {
                return triggerServerError(getUserMessageValidate(getMessageCaptchaInvalid(), []));
            }
        }

        do_action("before_reset_password_check", $inputs);

        $error = validationUserFormInputs($inputs, [
            "client_via" => addKeyToArray(getOTPActionForViaList(true))
        ], []);

        if ($error['message']) {
            return triggerServerError($error);
        }

        $user  = new \App\Models\User;

        $client_IDs = getUserClientIDList();

        foreach ($client_IDs as $client_ID) {
            $user = $user->orWhere($client_ID, $inputs['client_id']);
        }

        $user = $user->first();

        if (!$user) {
            return triggerServerError(getUserMessageValidate(__local("x-field does not exists"), ["client_id"]));
        }

        // check user is only blocked
        if (getTypeStatus($user) == "deactive_block") {
            $checkBlocked = checkUserBlocked($user, "back");
            if (is_object($checkBlocked)) return $checkBlocked;
        }


        $response = sendOTPToClient($user, "reset/password", "reset_password", $via, dechex(randomInteger(100000, 1000000)));
        if (is_a($response, 'Illuminate\Http\RedirectResponse')) {
            return $response;
        } else if (is_string($response)) {
            $redirect = $response;
        }

        return redirect($redirect);
    }

    public function pub_verify_client($user_hash_id, $id, $dechex_hash = "")
    {

        $OTP = OTP::where("user_hash_id", $user_hash_id)->where("id", $id)->where("expired", false)->where("seen", false)->get();
        $OTP = $OTP->first();

        abortByEntity($OTP);
        abortByExpire(getTypeDateExpired($OTP), getTypeAttr($OTP, "expired"));

        // make it global for accessing title and other data
        generateGlobalTitle(new User);


        return view("dashboard.user_panel.verify", [
            'DB' => $OTP,
            'hexdec' => $dechex_hash ? hexdec($dechex_hash) : $dechex_hash,
            'route_args' => [
                "user_hash_id" => $user_hash_id,
                "id" => $id,
                "dechex_hash" => $dechex_hash
            ]
        ]);
    }

    public function logout()
    {
        if (auth()->check()) {
            auth()->logout();
            return redirect(getTheRoute("user", "signin.form", []));
        } else {
            return back();
        }
    }
}
