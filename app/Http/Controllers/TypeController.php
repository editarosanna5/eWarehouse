<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class TypeController extends Controller {
    public function index() {
        $types = DB::select(DB::raw("SELECT * FROM Types ORDER BY id"));

        if ($types != null) {
            foreach ($types as $type) {
                echo "id = " . $type->id . ", type_name = " . $type->type_name . "<br>";
            }
        } else {
            echo "No Type found";
        }
    }

    public function show($id) {
        $type = DB::select(DB::raw("SELECT * FROM Types WHERE id = $id"));
        
        if ($type != null) {
            echo "id = " . $type[0]->id . ", type_name = " . $type[0]->type_name . "<br>";
        } else {
            echo "Type of id = {$id} does not exist.";
        }
    }

    public function create(Request $request) {
        $type = DB::select(DB::raw("SELECT * FROM Types WHERE type_name = \"$request->type_name\""));

        if ($type == null) {
            $type = DB::insert(DB::raw("INSERT INTO Types (type_name) VALUES (\"$request->type_name\")"));
            
            echo "Type {$request->type_name} added.";
        } else {
            echo "Type {$request->type_name} already exist.";
        }
    }

    public function update(Request $request, $id) {
        $type = DB::select(DB::raw("SELECT id FROM Types WHERE id = $id"));

        if ($type != null) {
            $type = DB::select(DB::raw("SELECT id FROM Types WHERE type_name = \"$request->type_name\""));
            
            if ($type == null) {
                $type = DB::update(DB::raw("UPDATE Types SET
                type_name = \"$request->type_name\"

                WHERE id = $id
            "));

            echo "Type with id = {$id} updated!";
            } else {
                echo "Type {$request->type_name} already exist.";
            }
        } else {
            echo "Type of id = {$id} does not exist";
        }
    }

    public function delete($id) {
        $type = DB::select(DB::raw("SELECT id FROM Types WHERE id = $id"));

        if ($type != null) {
            $type = DB::delete(DB::raw("DELETE FROM Types WHERE id = $id"));

            echo "Type with id = " . $type[0]->id . " deleted!";
        } else {
            echo "Type of id = {$id} does not exist";
        }
    }
}