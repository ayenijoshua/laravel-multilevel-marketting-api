<?php
namespace App\Repositories\Traits;

use Illuminate\Support\Facades\DB;
/**
 * user genealogy trait
 */
Trait Genealogy{

    /**
     * make user (it could be a referrer) a parent
     */
    public function makeUserAParent($ref_id,$child_id,$level_id){
        $this->transaction(function() use ($ref_id,$child_id,$level_id){
            $query_1 = DB::table('children')->select('id')->where('parent_id',$ref_id)->where('level_id',$level_id)->get(); //Children::where('parent_id',$ref_id)->get(); //"select parent_id from children where parent_id=$ref_id and level_id=$level_id";
            if($query_1->count() < 2 && !$this->checkDuplicateChildId($child_id,$level_id)) {
                $query = DB::table('children')->insert([
                    'child_id'=>$child_id,
                    'parent_id'=>$ref_id,
                    'level_id'=>$level_id
                ]);//"insert into children (child_id,parent_id,level_id) values('$child_id','$ref_id','$level_id')");
                if(!is_null($this->checkIfRefHasAParent($ref_id)) ){
                    $grandparent_id = $this->checkIfRefHasAParent($ref_id);
                    $parent_id = $ref_id;
                    $grandchild_id = $child_id;
                    // make the registrant// a grand child and make the ref a grand parent
                    $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $level_id); 
                    //make registrant a great_grandchild
                }
                if($level_id == 0){
                    if(!is_null($this->checkIfRefHasAGrandParent($ref_id))){
                        $grandparent_id = $this->checkIfRefHasAGrandParent($ref_id);
                        if(!is_null($this->checkIfRefHasAParent($grandparent_id))){
                            $great_grandparent_id = $this->checkIfRefHasAParent($grandparent_id);
                            $great_grandchild_id = $grandchild_id;
                            $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $level_id);
                        }  
                    }
                } 
            }
            if($query_1->count() == 2 && !$this->checkDuplicateChildId($child_id,$level_id)) {
                $children = $this->findChildren($ref_id,$level_id);
                if($children != null){
                    foreach ($children as $value){
                        $value = $value->child_id;
                        $query_2 = DB::table('children')->select('parent_id')->where(['parent_id'=>$value,'level_id'=>$level_id])->get();
                        if($query_2->count() < 2 && !$this->checkDuplicateChildId($child_id,$level_id)) {
                            $query = DB::table('children')->insert([
                                'child_id'=>$child_id,
                                'parent_id'=>$value,
                                'level_id'=>$level_id
                            ]); //("insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')"); //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                            $grandchild_id = $child_id;
                            $parent_id = $value;
                            $grandparent_id = $ref_id;
                            $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $level_id);
                            if($level_id==0){
                                if(!is_null($this->checkIfRefHasAGrandParent($ref_id))){
                                    $great_grandparent_id = $this->checkIfRefHasAGrandParent($ref_id);
                                    $great_grandchild_id = $grandchild_id;
                                    $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $level_id);     
                                }
                            }
                            break;
                        }
                    }
                    if($level_id==0){
                        $grandchildren = $this->findGrandChildren($ref_id,$level_id);
                        if(!is_null($grandchildren)){
                            foreach ($grandchildren as $value){
                                $value = $value->grandchild_id;
                                $query = DB::table('children')->select('parent_id')->where(['parent_id'=>$value,'level_id'=>$level_id])->get();//"select parent_id from children where parent_id=$value and level_id=$level_id";
                                if($query->count() < 2 && !$this->checkDuplicateChildId($child_id,$level_id)) {
                                    $query = DB::table('children')->insert([
                                        'child_id'=>$child_id,
                                        'parent_id'=>$value,
                                        'level_id'=>$level_id
                                    ]); //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                                    if(!is_null($this->checkIfRefHasAParent($value)) ){
                                        $grandparent_id = $this->checkIfRefHasAParent($value);
                                        $parent_id = $value;
                                        $grandchild_id = $this->child_id;
                                        // make the registrant// a grand child and make the ref a grand parent
                                        $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $level_id); 
                                        //make registrant a great_grandchild
                                    }
                                    $great_grandchild_id = $child_id;
                                    $parent_id = $value;
                                    $great_grandparent_id = $ref_id;
                                    $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $level_id);
                                    break;
                                }
                            }
                            $great_grandchildren = $this->findGreatGrandChildren($ref_id,$level_id);
                            if(!is_null($great_grandchildren)){
                                foreach ($great_grandchildren as $value){
                                    $value = $value->great_grandchild_id;
                                    $query = DB::table('children')->select('parent_id')->where(['parent_id'=>$value,'level_id'=>$level_id])->get();
                                    if($query->count() < 2 && !$this->checkDuplicateChildId($child_id,$level_id)) {
                                        $query = DB::table('children')->insert([
                                            'child_id'=>$child_id,
                                            'parent_id'=>$value,
                                            'level_id'=>$level_id
                                        ]); //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                                        if(!is_null($this->checkIfRefHasAParent($value)) ){
                                            $grandparent_id = $this->checkIfRefHasAParent($value);
                                            $parent_id = $value;
                                            $grandchild_id = $child_id;
                                            // make the registrant// a grand child and make the ref a grand parent
                                            $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $level_id); 
                                            //make registrant a great_grandchild
                                            if($this->checkIfRefHasAParent($grandparent_id)){
                                                $great_grandchild_id = $child_id;
                                                $parent_id = $value;
                                                $great_grandparent_id = $this->checkIfRefHasAParent( $grandparent_id);
                                                $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $level_id);
                                            }
                                        }
                                        break;
                                    }
                                }
                            }

                        }

                    }
                }
                
            }
        },2);
    }

    /**
     * make user  a greeat grand parent
     */
    private function makeUserAGrandParent($grandchild_id,$parent_id,$grandparent_id,$level_id) {
        $query = DB::table('grand_children')->insert([
            'grandchild_id'=>$grandchild_id,
            'parent_id'=>$parent_id,
            'grandparent_id'=>$grandparent_id,
            'level_id'=>$level_id
        ]);
    }

    /**
     * make user a greartgrand parent
     */
    private function makeUserAGreatGrandParent($great_grandchild_id,$parent_id,$great_grandparent_id,$level_id) {
        $query = DB::table('great_grand_children')->insert([
            'great_grandchild_id'=>$great_grandchild_id,
            'parent_id'=>$parent_id,
            'great_grandparent_id'=>$great_grandparent_id,
            'level_id'=>$level_id
        ]);
    }

    /**
     * check if the referral has a parent
     * can also be used to check if user has a parent
     * @return - parent_id
     */
    private function checkIfRefHasAParent($ref_id,$level_id=null){
        if(!is_null($level_id)){
            $res = DB::table('children')->select('parent_id')->where(['child_id'=>$ref_id,'level_id'=>$level_id])->first();
        }else{
            $res = DB::table('children')->select('parent_id')->where('child_id',$ref_id)->first(); 
        }
        //Children::where('child_id',$ref_id)->value('parent_id'); //"select parent_id from children where child_id=$ref_id limit 1";
        if(!is_null($res)){
            return $res->parent_id;
        } else {
            return null;
        }
    }

    /**
     * check if the referral has a grandparent
     * @return - grandparent_id
     */
    private function checkIfRefHasAGrandParent($ref_id){
        $res = DB::table('children')->where('parent_id',$ref_id)->first(); //"select grandparent_id from grandchildren where parent_id=$ref_id limit 1";
        if(!is_null($res)){
            return $res->grandparent_id;
        } else {
            return null;
        }
    }

    /**
     * check for duplicate child
     * @return - boolean
     */
    public function checkDuplicateChildId($child_id,$level_id) {
        $res = DB::table('children')->select('id')->where(['child_id'=>$child_id,'level_id'=>$level_id])->first(); //"select child_id from children where child_id = $child_id and level_id=$level_id";
        if(!is_null($res)){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * find grandchildren
     * @return - grandchild_id
     */
    private function findGrandChildren($ref_id,$level_id) {
        $res = DB::table('grand_children')->select('grand_child_id')->where(['grand_parent_id'=>$ref_id,'level_id'=>$level_id])->get();// "select grandchild_id from grandchildren where grandparent_id=$ref_id and level_id=$level_id";
        if($res->count() > 0){
        return $res;
        }
        return null; 
        
    }

    public function findGreatGrandChildren($ref_id,$level_id){
        $res = DB::table('great_grand_children')->select('great_grand_child_id')->where(['great_grand_parent_id'=>$ref_id,'level_id'=>$level_id])->get();// "select grandchild_id from grandchildren where grandparent_id=$ref_id and level_id=$level_id";
        if($res->count() > 0){
        return $res;
        }
        return null; 
    }

    /**
     * find children
     * @return - child_id
     */
    public function findChildren($ref_id,$level_id,$check_for_level_movement=true) {
        $res = DB::table('children')->select('child_id')->where(['parent_id'=>$ref_id,'level_id'=>$level_id])->get();//"select child_id from children where parent_id='$ref_id' and level_id='$level_id'";
        if($check_for_level_movement){
            if($res->count() == 2){ // change back to 2
                return $res;
            }
                return null;
        }else{
            if($res->count() > 0){
                return $res;
            }
                return null;
        }
    }

    /**
     * move user to the next level
     * @return - void
     */
    public function moveUserToNextLevel($user_id,$level_id) {
        $this->user_id = $user_id;
        $this->level_id = $level_id;
        $user = $this->get($user_id);
        if($this->level_id == 7){
            $this->withdrawal->cashOut($user);
            $this->incentive->incentiveCashOut($user);
            // $request = new \Illuminate\Http\Request;
            // (new \App\Http\Controllers\WithdrawalController($request))->userCashOut($user);
            // (new \App\Http\Controllers\IncentiveController($request))->incentiveCashOut($user);
        }
        DB::transaction(function() use ($user,$level_id){
            $request = new \Illuminate\Http\Request;
            if($user->cycled_out==1){
                return;
            }
            if($level_id == 7){
                //(new \App\Http\Controllers\WithdrawalController($request))->userCashOut($user);
                //(new \App\Http\Controllers\IncentiveController($request))->incentiveCashOut($user);
                $old_uuid_array = explode(',',$user->uuids);
                $uuid = $user->uuid;
                array_push($old_uuid_array,$uuid);
                // if($old_uuid_array[0]==""){
                //     array_shift($old_uuid_array);
                // }
                
                //dd($old_uuid_array);
                $new_uuids = implode(',',$old_uuid_array);
                //dd($new_uuids);
                $user->uuids = $new_uuids;
                $user->cycled_out = 1;
                $user->is_approved = 0;
                $user->level_id = 0;
                $user->uuid = (new \App\Http\Controllers\UserController($request))->generateUUId();
                $user->update();
                return;
            }else{
                $user->level_id = $level_id;
                $user->update();
                
                $parent_id = $this->checkIfRefHasAParent($user->uuid, 0);
                $children = $this->findChildren($user->uuid,0,false);
                $grand_children = $this->findGrandChildren($user->uuid,0,false);
                $great_grand_children = $this->findGreatGrandChildren($user->uuid,0,false);
                //$children_array = [];
                if(!is_null($children)){
                    foreach($children as $child){
                        // array_push($children_array,$child->child_id);
                        $direct_child = User::where('uuid',$child->child_id)->first();
                        if($direct_child->level_id == $level_id){ // if child(user) exists in the next level
                            DB::table('children')->where(['child_id'=>$child->child_id,'level_id'=>$level_id])->update(['parent_id'=>$user->uuid]);// update to make the child a child of his initial parent
                        }
                    }

                }
                if(!is_null($parent_id)){
                    $parent2_id = $this->checkIfRefHasAParent($parent_id, 0); //grandparent
                    $parent3_id = $this->checkIfRefHasAParent($parent2_id, 0);
                    $parent4_id = $this->checkIfRefHasAParent($parent3_id, 0); 
                    $parent5_id = $this->checkIfRefHasAParent($parent4_id, 0);
                    $parent6_id = $this->checkIfRefHasAParent($parent5_id, 0);
                    $res = DB::table('users')->select('id')->where(['uuid'=>$parent_id,'level_id'=>$level_id])->first(); //$this->safeQuery("select user_id from users where user_id=$parent_id and level_id=$level_id");
                    if(!is_null($res)){
                        $this-makeRefrerrerAParent($parent_id,$user->uuid,$level_id);
                        return;
                    }elseif(!is_null(DB::table('users')->select('id')->where(['uuid'=>$parent2_id,'level_id'=>$level_id])->first())){
                        $this-makeRefrerrerAParent($parent2_id,$user->uuid,$level_id);
                        return;
                    }elseif(!is_null(DB::table('users')->select('id')->where(['uuid'=>$parent3_id,'level_id'=>$level_id])->first())){
                        $this-makeRefrerrerAParent($parent3_id,$user->uuid,$level_id);
                        return;
                    }elseif(!is_null(DB::table('users')->select('id')->where(['uuid'=>$parent4_id,'level_id'=>$level_id])->first())){
                        $this-makeRefrerrerAParent($parent4_id,$user->uuid,$level_id);
                        return;
                    }elseif(!is_null(DB::table('users')->select('id')->where(['uuid'=>$parent5_id,'level_id'=>$level_id])->first())){
                        $this-makeRefrerrerAParent($parent5_id,$user->uuid,$level_id);
                        return;
                    }else{
                        if(!is_null(DB::table('users')->select('id')->where(['uuid'=>$parent6_id,'level_id'=>$level_id])->first())){
                            $this-makeRefrerrerAParent($parent6_id,$user->uuid,$level_id);
                        } 
                    }   
                }
            }
            
        },2); 
    }

    /**
    * get level geneology structure
    */  
    public function getLevelTreeStructure($user_id,$history=null,$id=null){
        if($history && User::find($id)){
            $level_id = 6;//User::findOrFail($user_id)->level_id;
            $children = $this->findChildren($user_id,6,false);
            $str =  // parent
            "'name' :' ".User::find($id)->username."',
                'stage' :'Stage 6',
                'img': '".User::find($id)->user_img."',
                ";

            if($children){
                $user = \App\User::class;
                $str .= "'children' : [";
                    foreach($children as $child){
                        $str .="{
                        'name' : '".$user::where('uuid',$child->child_id)->first()->username."',
                        'stage' : '".$user::where('uuid',$child->child_id)->first()->level->level."',
                        'img': '".User::where('uuid',$child->child_id)->first()->user_img."',
                        'collapsed': true,";
                        
                        if($this->findChildren($child->child_id,$level_id,false)){
                            $str .=" 'children' : [";
                            foreach($this->findChildren($child->child_id,$level_id,false) as $child1){
                                $str .="{
                                    'name' : '".$user::where('uuid',$child1->child_id)->first()->username."',
                                    'stage' : '".$user::where('uuid',$child1->child_id)->first()->level->level."',
                                    'img': '".User::where('uuid',$child1->child_id)->first()->user_img."',
                                    ";
    
                                if($this->findChildren($child1->child_id,$level_id,false)){
                                    $str .=" 'children' : [";
                                    foreach($this->findChildren($child1->child_id,$level_id,false) as $child2){
                                        $str .="{
                                        'name' : '".$user::where('uuid',$child2->child_id)->first()->username."',
                                        'stage' : '".$user::where('uuid',$child2->child_id)->first()->level->level."',
                                        'img': '".User::where('uuid',$child2->child_id)->first()->user_img."',
                                        },";
                                    }
                                    $str.="]";
                                    
                                }
                                $str.="},";

                            }
                            $str .= "]";
                        }
                        $str.="},";
                    }
                    return $str."]";
            }else{return $str;}
        }else{
            if(User::where('uuid',$user_id)->first()){
                $level_id = User::where('uuid',$user_id)->first()->level_id;//User::findOrFail($user_id)->level_id;
                $children = $this->findChildren($user_id,$level_id,false);
                
                $str =  // parent
                "'name' :' ".User::where('uuid',$user_id)->first()->username."',
                    'stage' :'".User::where('uuid',$user_id)->first()->level->level."',
                    'img': '".User::where('uuid',$user_id)->first()->user_img."',
                    ";

                if(!is_null($children)){
                    $user = \App\User::class;
                    $str .= "'children' : [";
                        foreach($children as $child){
                            $str .="{
                            'name' : '".$user::where('uuid',$child->child_id)->value('username')."',
                            'stage' : '".\App\Level::where('id',$user::where('uuid',$child->child_id)->value('level_id'))->value('level')."',
                            'img': '".User::where('uuid',$child->child_id)->value('user_img')."',
                            'collapsed': false,";
                            
                            if($this->findChildren($child->child_id,$level_id,false)){
                                $str .=" 'children' : [";
                                foreach($this->findChildren($child->child_id,$level_id,false) as $child1){
                                    $str .="{
                                        'name' : '".$user::where('uuid',$child1->child_id)->first()->username."',
                                        'stage' : '".$user::where('uuid',$child1->child_id)->first()->level->level."',
                                        'img': '".User::where('uuid',$child1->child_id)->value('user_img')."',
                                        'collapsed': false,";
        
                                    if($this->findChildren($child1->child_id,$level_id,false)){
                                        $str .=" 'children' : [";
                                        foreach($this->findChildren($child1->child_id,$level_id,false) as $child2){
                                            $str .="{
                                            'name' : '".$user::where('uuid',$child2->child_id)->first()->username."',
                                            'stage' : '".$user::where('uuid',$child2->child_id)->first()->level->level."',
                                            'img': '".User::where('uuid',$child2->child_id)->first()->user_img."',
                                            },";
                                        }
                                        $str.="]";
                                        
                                    }
                                    $str.="},";

                                }
                                $str .= "]";
                            }
                            $str.="},";
                        }
                        return $str."]";
                }else{return $str;}
            }
        }   
    }

    /**
    * get downline geneology structure
    */
    public function getDownlineTreeStructure($user_id,$history=null,$id=null){
        if($history && $this->get($id)){
            $str = // parent
            "'name' :' ".$this->get($id)->username."',
            'stage' :'Stage 6',
            'img': '".$this->get($id)->user_img."',
            ";
            $level_id = 0;
            $children = $this->findChildren($user_id,$level_id,false);
                if($children){
                    $user = \App\User::class;
                    $str .= "\"children\" : [";
                        foreach($children as $child){
                            $str .="{
                            \"name\" : \"".$user::where('uuid',$child->child_id)->first()->username."\",
                            \"stage\" : \"".$user::where('uuid',$child->child_id)->first()->level->level."\" ,
                            'img': '".User::where('uuid',$child->child_id)->first()->user_img."',
                            ";
                            
                            if($this->findChildren($child->child_id,$level_id,false)){
                                $str .=" \"children\" : [";
                                foreach($this->findChildren($child->child_id,$level_id,false) as $child1){
                                    $str .="{
                                        \"name\" : \"".$user::where('uuid',$child1->child_id)->first()->username."\", 
                                        \"stage\" : \"".$user::where('uuid',$child1->child_id)->first()->level->level."\",
                                        'img': '".User::where($child1->child_id)->first()->user_img."',
                                        ";
            
                                    if($this->findChildren($child1->child_id,$level_id,false)){
                                        $str .="\"children\" : [";
                                        foreach($this->findChildren($child1->child_id,$level_id,false) as $child2){
                                            $str .="{
                                            \"name\" : \"".$user::where('uuid',$child2->child_id)->first()->username."\",
                                            \"stage\" : \"".$user::where('uuid',$child2->child_id)->first()->level->level."\",
                                            'img': '".User::where('uuid',$child2->child_id)->first()->user_img."'
                                            ,";
            
                                            if($this->findChildren($child2->child_id,$level_id,false)){
                                                $str .="\"children\" : [";
                                                foreach($this->findChildren($child2->child_id,$level_id,false) as $child3){
                                                    $str .="{
                                                    \"name\" : \"".$user::where('uuid',$child3->child_id)->first()->username."\",
                                                    \"stage\" : \"".$user::where('uuid',$child3->child_id)->first()->level->level."\",
                                                    'img': '".User::where('uuid',$child3->child_id)->first()->user_img."',
                                                    "; 

                                                    if($this->findChildren($child3->child_id,$level_id,false)){
                                                        $str .="\"children\" : [";
                                                        foreach($this->findChildren($child3->child_id,$level_id,false) as $child4){
                                                            $str .="{
                                                            \"name\" : \"".$user::where('uuid',$child4->child_id)->first()->username."\",
                                                            \"stage\" : \"".$user::where('uuid',$child4->child_id)->first()->level->level."\",
                                                            'img': '".User::where('uuid',$child4->child_id)->first()->user_img."',
                                                            "; 

                                                            if($this->findChildren($child4->child_id,$level_id,false)){
                                                                $str .="\"children\" : [";
                                                                foreach($this->findChildren($child4->child_id,$level_id,false) as $child5){
                                                                    $str .="{
                                                                    \"name\" : \"".$user::where('uuid',$child5->child_id)->first()->username."\",
                                                                    \"stage\" : \"".$user::where('uuid',$child5->child_id)->first()->level->level."\",
                                                                    'img': '".User::where('uuid',$child5->child_id)->first()->user_img."',
                                                                    "; 

                                                                    if($this->findChildren($child5->child_id,$level_id,false)){
                                                                        $str .="\"children\" : [";
                                                                        foreach($this->findChildren($child5->child_id,$level_id,false) as $child6){
                                                                            $str .="{
                                                                            \"name\" : \"".$user::where('uuid',$child6->child_id)->first()->username."\",
                                                                            \"stage\" : \"".$user::where('uuid',$child6->child_id)->first()->level->level."\",
                                                                            'img': '".User::where('uuid',$child6->child_id)->first()->user_img."',
                                                                            },"; 
                                                                        }
                                                                        $str.="]";
                                                                    }
                                                                    $str.="},";
                                                                }
                                                                $str.="]";
                                                            }
                                                            $str.="},";
                                                        }
                                                        $str.="]";
                                                    }
                                                    $str.="},";
                                                }
                                                $str.="]";
                                                
                                            } 
                                            $str.="},";
                                        }
                                        $str.="]";
                                        
                                    }
                                    $str.="},";
            
                                }
                                $str .= "]";
                            }
                            $str.="},";
                        }
                        return $str."]";
                }else{return $str;}
        }else{
            $level_id = 0;
            $children = $this->findChildren($user_id,$level_id,false);
        
            if(User::where('uuid',$user_id)->first()){
                    $str = // parent
                "\"name\" : \"".User::where('uuid',$user_id)->first()->username."\",
                    \"stage\" :\"".User::where('uuid',$user_id)->first()->level->level."\",
                    'img': '".User::where('uuid',$user_id)->first()->user_img."',
                    ";
    
                if($children){
                    $user = \App\User::class;
                    $str .= "\"children\" : [";
                        foreach($children as $child){
                            $str .="{
                            \"name\" : \"".$user::where('uuid',$child->child_id)->value('username')."\",
                            \"stage\" : \"".\App\Level::where('id',$user::where('uuid',$child->child_id)->value('level_id'))->value('level')."\" ,
                            'img': '".User::where('uuid',$child->child_id)->value('user_img')."',
                            ";
                            
                            if($this->findChildren($child->child_id,$level_id,false)){
                                $str .=" \"children\" : [";
                                foreach($this->findChildren($child->child_id,$level_id,false) as $child1){
                                    $str .="{
                                        \"name\" : \"".$user::where('uuid',$child1->child_id)->first()->username."\", 
                                        \"stage\" : \"".$user::where('uuid',$child1->child_id)->first()->level->level."\",
                                        'img': '".User::where('uuid',$child1->child_id)->first()->user_img."',
                                        ";
            
                                    if($this->findChildren($child1->child_id,$level_id,false)){
                                        $str .="\"children\" : [";
                                        foreach($this->findChildren($child1->child_id,$level_id,false) as $child2){
                                            $str .="{
                                            \"name\" : \"".$user::where('uuid',$child2->child_id)->first()->username."\",
                                            \"stage\" : \"".$user::where('uuid',$child2->child_id)->first()->level->level."\",
                                            'img': '".User::where('uuid',$child2->child_id)->first()->user_img."'
                                            ,";
            
                                            if($this->findChildren($child2->child_id,$level_id,false)){
                                                $str .="\"children\" : [";
                                                foreach($this->findChildren($child2->child_id,$level_id,false) as $child3){
                                                    $str .="{
                                                    \"name\" : \"".$user::where('uuid',$child3->child_id)->first()->username."\",
                                                    \"stage\" : \"".$user::where('uuid',$child3->child_id)->first()->level->level."\",
                                                    'img': '".User::where('uuid',$child3->child_id)->first()->user_img."',
                                                    "; 

                                                    if($this->findChildren($child3->child_id,$level_id,false)){
                                                        $str .="\"children\" : [";
                                                        foreach($this->findChildren($child3->child_id,$level_id,false) as $child4){
                                                            $str .="{
                                                            \"name\" : \"".$user::where('uuid',$child4->child_id)->first()->username."\",
                                                            \"stage\" : \"".$user::where('uuid',$child4->child_id)->first()->level->level."\",
                                                            'img': '".User::where('uuid',$child4->child_id)->first()->user_img."',
                                                            "; 

                                                            if($this->findChildren($child4->child_id,$level_id,false)){
                                                                $str .="\"children\" : [";
                                                                foreach($this->findChildren($child4->child_id,$level_id,false) as $child5){
                                                                    $str .="{
                                                                    \"name\" : \"".$user::where('uuid',$child5->child_id)->first()->username."\",
                                                                    \"stage\" : \"".$user::where('uuid',$child5->child_id)->first()->level->level."\",
                                                                    'img': '".User::where('uuid',$child5->child_id)->first()->user_img."',
                                                                    "; 

                                                                    if($this->findChildren($child5->child_id,$level_id,false)){
                                                                        $str .="\"children\" : [";
                                                                        foreach($this->findChildren($child5->child_id,$level_id,false) as $child6){
                                                                            $str .="{
                                                                            \"name\" : \"".$user::where('uuid',$child6->child_id)->first()->username."\",
                                                                            \"stage\" : \"".$user::where('uuid',$child6->child_id)->first()->level->level."\",
                                                                            'img': '".User::where('uuid',$child6->child_id)->first()->user_img."',
                                                                            },"; 
                                                                        }
                                                                        $str.="]";
                                                                    }
                                                                    $str.="},";
                                                                }
                                                                $str.="]";
                                                            }
                                                            $str.="},";
                                                        }
                                                        $str.="]";
                                                    }
                                                    $str.="},";
                                                }
                                                $str.="]";
                                                
                                            } 
                                            $str.="},";
                                        }
                                        $str.="]";
                                        
                                    }
                                    $str.="},";
            
                                }
                                $str .= "]";
                            }
                            $str.="},";
                        }
                        return $str."]";
                }else{return $str;}
            }
        }
    }

    /**
     * total downlines at each level
     */
    public function totalLevelDownlines($user,$level=null){
        $level_id = !is_null($level)? $level : $user->level_id;
        $children_array = [];
        $grand_children_array = [];
        $total_downlines = 0;

        $children = $this->findChildren($user->uuid, $level_id,false);
        if(!is_null($children)){ //check if user has children
            //$array = [];
            foreach($children as $child){//loop trough the children and push their id into a stack
                array_push($children_array,$child->child_id);
                
                $grandchildren = $this->findChildren($child->child_id, $level_id,false);
                if(!is_null($grandchildren)){//if user has grandchildren
                    foreach($grandchildren as $child1){//loop trough the grandchildren and push their id into a stack
                    array_push($grand_children_array,$child1->child_id);
                    }
                }
            }
        }
        $total_downlines =  count($children_array) + count($grand_children_array);
        return $total_downlines;
    }
}