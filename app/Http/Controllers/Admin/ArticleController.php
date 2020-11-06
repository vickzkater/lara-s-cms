<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Intervention\Image\Facades\Image;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\Topic;
use App\Models\Article;
use App\Models\ArticleTopic;

class ArticleController extends Controller
{
    // set this module
    private $module = 'Article';

    // SET THIS OBJECT/ITEM NAME
    private $item = 'article';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        return view('admin.article.list', compact('data'));
    }

    public function get_data(Datatables $datatables, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return response()->json([
                'status' => 'false',
                'message' => $authorize['message']
            ]);
        }

        // GET THE DATA
        $query = Article::select(
            'articles.id',
            'articles.title',
            'articles.slug',
            'articles.thumbnail',
            'articles.keywords',
            'articles.content',
            'articles.status',
            'articles.created_at',
            'articles.updated_at',
            'articles.deleted_at',
            DB::raw('GROUP_CONCAT(topics.name SEPARATOR " | ") AS topics')
        )
            ->leftJoin('article_topic', 'articles.id', 'article_topic.article_id')
            ->leftJoin('topics', 'article_topic.topic_id', 'topics.id')
            ->groupBy(
                'articles.id'
            );

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<button class="btn btn-xs btn-danger" title="' . ucwords(lang('draft', $this->translation)) . '" onclick="if(confirm(\'' . lang('Are you sure to publish this #item?', $this->translation, ['#item' => $this->item]) . '\')) window.location.replace(\'' . route('admin.article.enable', $data->id) . '\');">' . ucwords(lang('draft', $this->translation)) . '</button>';
                }
                return '<button class="btn btn-xs btn-success" title="' . ucwords(lang('published', $this->translation)) . '" onclick="if(confirm(\'' . lang('Are you sure to set this #item as Draft & unpublish it?', $this->translation, ['#item' => $this->item]) . '\')) window.location.replace(\'' . route('admin.article.disable', $data->id) . '\');">' . ucwords(lang('published', $this->translation)) . '</button>';
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.article.edit', $data->id) . '" class="btn btn-xs btn-primary" title="' . ucwords(lang('edit', $this->translation)) . '"><i class="fa fa-pencil"></i>&nbsp; ' . ucwords(lang('edit', $this->translation)) . '</a>';

                $html .= '<form action="' . route('admin.article.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
                <button type="submit" class="btn btn-xs btn-danger" title="' . ucwords(lang('delete', $this->translation)) . '"><i class="fa fa-trash"></i>&nbsp; ' . ucwords(lang('delete', $this->translation)) . '</button></form>';

                return $html;
            })
            ->addColumn('image_item', function ($data) {
                return '<img src="' . asset($data->thumbnail) . '" style="max-width: 200px;max-height: 200px;display: block;margin-left: auto;margin-right: auto;">';
            })
            ->addColumn('topic', function ($data) {
                return $data->topics;
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->editColumn('keywords', function ($data) {
                return str_replace(',', ', ', $data->keywords);
            })
            ->editColumn('updated_at', function ($data) {
                return Helper::time_ago(strtotime($data->updated_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
            })
            ->rawColumns(['item_status', 'action', 'image_item', 'topic'])
            ->toJson();
    }

    public function create()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        $topics = Topic::orderBy('name')->get();

        return view('admin.article.form', compact('topics'));
    }

    public function do_create(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // LARAVEL VALIDATION
        $validation = [
            'title' => 'required',
            'thumbnail' => 'required|image|max:2048',
            'summary' => 'required',
            'topic' => 'required',
            'v_element_type' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'image' => ':attribute ' . lang('must be an image', $this->translation),
            'max' => ':attribute ' . lang('may not be greater than #item', $this->translation, ['#item' => '2MB'])
        ];
        $names = [
            'title' => ucwords(lang('title', $this->translation)),
            'thumbnail' => ucwords(lang('thumbnail', $this->translation)),
            'summary' => ucwords(lang('summary', $this->translation)),
            'topic' => ucwords(lang('topic', $this->translation)),
            'v_element_type' => ucwords(lang('article content', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // INSERT NEW DATA
        $data = new Article();

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $title = Helper::validate_input_text($request->title);
        if (!$title) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format data for ', $this->translation) . ucwords(lang('title', $this->translation)));
        }
        $data->title = $title;

        $slug = Helper::generate_slug($title);
        if ($request->slug) {
            $slug = Helper::generate_slug($request->slug);
        }
        // MAKE SURE SLUG IS UNIQUE
        $slug = Helper::check_slug('articles', $slug);
        $data->slug = $slug;

        $data->keywords = Helper::validate_input_text($request->keywords);

        $data->summary = Helper::validate_input_text($request->summary);

        $data->author = Helper::validate_input_text($request->author);

        $posted_at = Helper::validate_input_text($request->posted_at);
        if ($posted_at) {
            $data->posted_at = Helper::convert_datepicker($posted_at);
        }

        $data->status = (int) $request->status;

        // PROCESSING IMAGE
        $dir_path = 'uploads/article/';
        $image_file = $request->file('thumbnail');
        $format_image_name = time() . '-thumbnail';
        $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
        $generate_thumbnail = true;
        $thumbnail_width = 750;
        $thumbnail_height = 300;
        $thumbnail_quality_percentage = 80;
        $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name, $allowed_extensions, $generate_thumbnail, $thumbnail_width, $thumbnail_height, $thumbnail_quality_percentage);
        if ($image['status'] != 'true') {
            // FAILED TO UPLOAD IMAGE
            return back()
                ->withInput()
                ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
        }
        $data->thumbnail = $dir_path . $image['thumbnail'];

        // PROCESSING CONTENT ELEMENT
        $types = $request->v_element_type;
        $sections = $request->v_element_section;
        $content_text = $request->v_element_content_text;
        $positions = $request->v_text_position;
        $content_image = $request->v_element_content_image;
        $content_video = $request->v_element_content_video;
        $full_content = [];
        foreach ($types as $key => $value) {
            // SAVE PER ELEMENT TYPE USING OBJECT
            $obj_content = new \stdClass();
            $obj_content->type = $value;
            $obj_content->section = $sections[$key];
            // VALIDATE CONTENT BASED ON TYPE
            switch ($obj_content->type) {
                case 'text':
                    $obj_content->text = $content_text[$key];
                    break;

                case 'image':
                    // PROCESSING IMAGE
                    $dir_path = 'uploads/article/content/';
                    $image_file = $content_image[$key];
                    $format_image_name = $key . '-content-' . time();
                    $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
                    $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name, $allowed_extensions);
                    if ($image['status'] != 'true') {
                        return back()
                            ->withInput()
                            ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
                    }
                    $obj_content->image = $dir_path . $image['data'];
                    break;

                case 'image & text':
                    // PROCESSING IMAGE
                    $dir_path = 'uploads/article/content/';
                    $image_file = $content_image[$key];
                    $format_image_name = $key . '-content-' . time();
                    $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
                    $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name);
                    if ($image['status'] != 'true') {
                        return back()
                            ->withInput()
                            ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
                    }
                    $obj_content->image = $dir_path . $image['data'];
                    $obj_content->text = $content_text[$key];
                    $obj_content->text_position = $positions[$key];
                    break;

                case 'video':
                    $obj_content->video = $content_video[$key];
                    break;

                case 'video & text':
                    $obj_content->video = $content_video[$key];
                    $obj_content->text = $content_text[$key];
                    $obj_content->text_position = $positions[$key];
                    break;

                case 'plain text':
                    $obj_content->text = $content_text[$key];
                    break;

                default:
                    return back()->withInput()->with('error', lang('Oops, there is an unknown content element type', $this->translation));
                    break;
            }
            $full_content[$key] = $obj_content;
        }
        $data->content = json_encode($full_content);

        // SAVE THE DATA
        if ($data->save()) {
            // SAVE THE SELECTED TOPIC(S)
            foreach ($request->topic as $item) {
                $topic = new ArticleTopic();
                $topic->article_id = $data->id;
                $topic->topic_id = $item;

                if (!$topic->save()) {
                    // SUCCESS BUT ERROR
                    return redirect()
                        ->route('admin.article.list')
                        ->with('success', lang('Successfully added a new #item : #name', $this->translation, ['#item' => $this->item, '#name' => $title]) . ', ' . lang('but failed to add #item(s) for it', $this->translation, ['#item' => lang('topics', $this->translation)]));
                }
            }

            // SUCCESS
            return redirect()
                ->route('admin.article.list')
                ->with('success', lang('Successfully added a new #item : #name', $this->translation, ['#item' => $this->item, '#name' => $title]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to add a new #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function edit($id)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Article::select(
            'articles.id',
            'articles.title',
            'articles.slug',
            'articles.thumbnail',
            'articles.author',
            'articles.posted_at',
            'articles.keywords',
            'articles.summary',
            'articles.content',
            'articles.status',
            DB::raw('GROUP_CONCAT(article_topic.topic_id) AS topic')
        )
            ->leftJoin('article_topic', 'articles.id', 'article_topic.article_id')
            ->where('articles.id', $id)
            ->groupBy(
                'articles.id',
                'articles.title',
                'articles.slug',
                'articles.thumbnail',
                'articles.author',
                'articles.posted_at',
                'articles.keywords',
                'articles.summary',
                'articles.content',
                'articles.status'
            )
            ->first();

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        $topics = Topic::orderBy('name')->get();

        return view('admin.article.form', compact('data', 'topics'));
    }

    public function do_edit($id, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET EXISTING DATA
        $data = Article::find($id);

        if (!$data) {
            return redirect()->route('admin.article.list')->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // LARAVEL VALIDATION
        $validation = [
            'title' => 'required',
            'summary' => 'required',
            'topic' => 'required',
            'v_element_type' => 'required'
        ];
        // if upload new image for THUMBNAIL
        if ($request->thumbnail) {
            $validation['thumbnail'] = 'required|image|max:2048';
        }
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'image' => ':attribute ' . lang('must be an image', $this->translation),
            'max' => ':attribute ' . lang('may not be greater than #item', $this->translation, ['#item' => '2MB'])
        ];
        $names = [
            'title' => ucwords(lang('title', $this->translation)),
            'thumbnail' => ucwords(lang('thumbnail', $this->translation)),
            'summary' => ucwords(lang('summary', $this->translation)),
            'topic' => ucwords(lang('topic', $this->translation)),
            'v_element_type' => ucwords(lang('article content', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $title = Helper::validate_input_text($request->title);
        if (!$title) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format data for ', $this->translation) . ucwords(lang('title', $this->translation)));
        }
        $data->title = $title;

        $slug = Helper::generate_slug($title);
        if ($request->slug) {
            $slug = Helper::generate_slug($request->slug);
        }
        // MAKE SURE SLUG IS UNIQUE
        if ($data->slug != $slug) {
            $slug = Helper::check_slug('article', $slug);
        }
        $data->slug = $slug;

        $data->keywords = Helper::validate_input_text($request->keywords);

        $data->summary = Helper::validate_input_text($request->summary);

        $data->author = Helper::validate_input_text($request->author);

        $posted_at = Helper::validate_input_text($request->posted_at);
        if ($posted_at) {
            $data->posted_at = Helper::convert_datepicker($posted_at);
        }

        $data->status = (int) $request->status;

        // if upload new image for THUMBNAIL
        if ($request->thumbnail) {
            // PROCESSING IMAGE
            $dir_path = 'uploads/article/';
            $image_file = $request->file('thumbnail');
            $format_image_name = time() . '-thumbnail';
            $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
            $generate_thumbnail = true;
            $thumbnail_width = 750;
            $thumbnail_height = 300;
            $thumbnail_quality_percentage = 80;
            $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name, $allowed_extensions, $generate_thumbnail, $thumbnail_width, $thumbnail_height, $thumbnail_quality_percentage);
            if ($image['status'] != 'true') {
                // FAILED TO UPLOAD IMAGE
                return back()
                    ->withInput()
                    ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
            }
            $data->thumbnail = $dir_path . $image['thumbnail'];
        }

        // PROCESSING CONTENT ELEMENT
        $types = $request->v_element_type;
        $sections = $request->v_element_section;
        $content_text = $request->v_element_content_text;
        $positions = $request->v_text_position;
        $content_image = $request->v_element_content_image;
        $content_video = $request->v_element_content_video;
        $full_content = [];
        // GET EXISTING DATA
        $exist_content = json_decode($data->content, true);
        foreach ($types as $key => $value) {
            // SAVE PER ELEMENT TYPE USING OBJECT
            $obj_content = new \stdClass();
            $obj_content->type = $types[$key];
            $obj_content->section = $sections[$key];
            // VALIDATE CONTENT BASED ON TYPE
            switch ($obj_content->type) {
                case 'text':
                    $obj_content->text = $content_text[$key];
                    break;

                case 'image':
                    // IF UPLOAD NEW IMAGE
                    if (isset($content_image[$key])) {
                        // GET OLD DATA FOR REMOVE THE IMAGE, IF EXIST
                        if (isset($exist_content[$key]['image'])) {
                            $old_content_img[] = $exist_content[$key]['image'];
                        }

                        // PROCESSING IMAGE
                        $dir_path = 'uploads/article/content/';
                        $image_file = $content_image[$key];
                        $format_image_name = $key . '-content-' . time();
                        $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
                        $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name, $allowed_extensions);
                        if ($image['status'] != 'true') {
                            return back()
                                ->withInput()
                                ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
                        }
                        $obj_content->image = $dir_path . $image['data'];
                    } else {
                        // GET OLD DATA
                        if (isset($exist_content[$key]['image'])) {
                            $obj_content->image = $exist_content[$key]['image'];
                        } else {
                            // ERROR - IMAGE IS REQUIRED FOR THIS ELEMENT
                            return back()
                                ->withInput()
                                ->with('error', lang('Oops, content element image type for Section #name is required. Please upload an image then try submit again.', $this->translation, ['#name' => $obj_content->section]));
                        }
                    }
                    break;

                case 'image & text':
                    // IF UPLOAD NEW IMAGE
                    if (isset($content_image[$key])) {
                        // GET OLD DATA FOR REMOVE THE IMAGE, IF EXIST
                        if (isset($exist_content[$key]['image'])) {
                            $old_content_img[] = $exist_content[$key]['image'];
                        }

                        // PROCESSING IMAGE
                        $dir_path = 'uploads/article/content/';
                        $image_file = $content_image[$key];
                        $format_image_name = $key . '-content-' . time();
                        $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
                        $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name, $allowed_extensions);
                        if ($image['status'] != 'true') {
                            return back()
                                ->withInput()
                                ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
                        }
                        $obj_content->image = $dir_path . $image['data'];
                    } else {
                        // GET OLD DATA
                        if (isset($exist_content[$key]['image'])) {
                            $obj_content->image = $exist_content[$key]['image'];
                        } else {
                            // ERROR - IMAGE IS REQUIRED FOR THIS ELEMENT
                            return back()
                                ->withInput()
                                ->with('error', lang('Oops, content element image type for Section #name is required. Please upload an image then try submit again.', $this->translation, ['#name' => $obj_content->section]));
                        }
                    }
                    $obj_content->text = $content_text[$key];
                    $obj_content->text_position = $positions[$key];
                    break;

                case 'video':
                    $obj_content->video = $content_video[$key];
                    break;

                case 'video & text':
                    $obj_content->video = $content_video[$key];
                    $obj_content->text = $content_text[$key];
                    $obj_content->text_position = $positions[$key];
                    break;

                case 'plain text':
                    $obj_content->text = $content_text[$key];
                    break;

                default:
                    return back()->withInput()->with('error', lang('Oops, there is an unknown content element type', $this->translation));
                    break;
            }
            $full_content[$key] = $obj_content;
        }
        $data->content = json_encode($full_content);

        if ($data->save()) {
            // SAVE THE SELECTED TOPIC(S)
            // BUT DELETE OLD TOPIC(S) FOR THIS ARTICLE FIRST
            ArticleTopic::where('article_id', $data->id)->delete();
            // THEN SAVE THE SELECTED TOPIC(S)
            foreach ($request->topic as $item) {
                $topic = new ArticleTopic();
                $topic->article_id = $data->id;
                $topic->topic_id = $item;

                if (!$topic->save()) {
                    // SUCCESS BUT ERROR
                    return redirect()
                        ->route('admin.article.list')
                        ->with('success', lang('Successfully added a new #item : #name', $this->translation, ['#item' => $this->item, '#name' => $title]) . ', ' . lang('but failed to add #item(s) for it', $this->translation, ['#item' => lang('topics', $this->translation)]));
                }
            }

            // SUCCESS
            return redirect()
                ->route('admin.article.edit', $id)
                ->with('success', lang('Successfully updated #item : #name', $this->translation, ['#item' => $this->item, '#name' => $title]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to update #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function delete(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        $id = $request->id;

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Article::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // SUCCESS
            return redirect()
                ->route('admin.article.list')
                ->with('success', lang('Successfully deleted #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->title]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to delete #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function enable($id)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Article::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->status = 1;

        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.article.list')
                ->with('success', lang('Successfully published #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->title]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to publish #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function disable($id)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Article::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.article.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->status = 0;

        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.article.list')
                ->with('success', lang('Successfully set as Draft #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->title]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to set as Draft #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
