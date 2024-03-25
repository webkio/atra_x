<?php

namespace App\Http\Controllers;

use App\Models\File;


class FileController extends Controller
{
    public function create()
    {
        // make it global for accessing title and other data
        generateGlobalTitle(new File);

        return view("dashboard.file_create", [
            'DB' => [],
            'action' => __FUNCTION__,
            'route_args' => []
        ]);
    }

    public function store($overloadedFile = [])
    {

        $files = $overloadedFile ? $overloadedFile : request('the_file');

        $source = request('source');

        $response = ["errors" => [], "success" => [], "progress" => null];

        do_action("before_files_" . __FUNCTION__ . "_check", $files);

        // check source
        if (!in_array($source, array_keys(getSourceFile()))) {
            return restMessageEncode(getUserMessageValidate(getUnAllowedMessage("source"), "source"));
        }

        $thePlusTime = time();

        $i = 0;
        $fileCount = count($files);
        foreach ($files as $file) {
            $thePlusTime .= "-" . randomInteger(1, 10) +  randomInteger($file->getSize() + 1, $file->getSize() + 10);
            $file_originalName = $file->getClientOriginalName();
            $size = convertByteToMB($file->getSize());

            if (75 < mb_strlen($file_originalName)) {
                $response["errors"][] = [
                    "message" => __local("file name is invalid"),
                    "data" => [$file_originalName],
                ];
                continue;
            }

            $file_originalNameExploded = explode(".", $file_originalName);
            $file_originalNameExploded = cleanTheArray($file_originalNameExploded);

            if (file_upload_max_size() < $file->getSize() || $size === 0) {
                $response["errors"][] = [
                    "message" => __local("file is more than allowed upload size or file is invalid"),
                    "data" => [$file_originalName],
                ];
                continue;
            }

            if (count($file_originalNameExploded) < 2) {
                $response["errors"][] = [
                    "message" => __local("must have (dot)Format"),
                    "data" => [$file_originalName],
                ];
                continue;
            }

            $file_extension = strtolower(end($file_originalNameExploded));

            if (isUnAllowedFormat($file_extension)) {
                $response["errors"][] = [
                    "message" => __local("for security reason cannot save this file"),
                    "data" => [$file_originalName]
                ];
                continue;
            }

            // create directory by year -> month -> *files
            $path = createDirectoryArchive(-3);

            $group_type = searchInGroupType($file_extension);
            // $thePlusTime for fix same for 2 file
            $new_file_name = $group_type . "-" . $thePlusTime . ".{$file_extension}";
            $finallPath = $path . SPE . $new_file_name;

            $file->storeAs($path, $new_file_name);

            $url_location = env('STORAGE_ROOT') . "/" . $finallPath;
            $tmp_url_location = str_replace("\\", "/", $url_location);
            $url_location = pathToURL($url_location);

            $dimension = [];

            // generate sub sizes
            $dimension = generateImageSizesByUploadedFile($group_type, $file_extension, $tmp_url_location);

            if (!$dimension) $dimension = null;

            $current_user = getCurrentUser();

            $file_row = File::create([
                "original_title" => $file_originalName,
                "current_title" => $new_file_name,
                "format" => $file_extension,
                "group_type" => $group_type,
                "size" => $size,
                "url" => $tmp_url_location,
                "dimension" => json_encode($dimension),
                "user_id" => getTypeID($current_user),
                "user_fullname" => getTypeFullname($current_user),
                "source" => $source,
            ]);



            $response["success"][] = [
                "message" => "uploaded !",
                "id" => getTypeID($file_row),
                "data" => [$file_originalName],
                "url" => $url_location,
            ];

            $i++;
        }

        $response['progress'] = str_replace(['x-current', 'x-all'], [$i, $fileCount], __local("x-current of x-all successfully uploaded !"));

        do_action("after_files_" . __FUNCTION__ . "_check", $response, $files);

        return $response;
    }

    public function index()
    {

        do_action("file.list");
        $file = File::where("id", "!=", 0);

        // make it global for accessing title and other data
        generateGlobalTitle(new File);

        $filterHtml = getTableHeadFile();
        $file = filterListHandler($file, $filterHtml, []);

        return view("dashboard.file_list", [
            'DB' => $file,
            'route_args' => []
        ]);
    }

    public function destroy()
    {
        $model = File::findOrfail(request("id"));
        $sizes = getSubSizesImageByFilename($model->current_title, false, $model);
        $sizes["full"] = $model->url;

        foreach ($sizes as $size) {
            $size = getElementByExplodePart($size, 1, 25);
            $size = join("/", $size);
            $size = storage_path($size);
            if (file_exists($size)) {
                unlink($size);
            }
        }

        return deleteType(getFullNamespaceByModel("File", "findOrfail"), [], "current_title");
    }
}
