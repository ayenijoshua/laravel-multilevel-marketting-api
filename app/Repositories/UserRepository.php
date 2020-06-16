<?php 
namespace App\Repositories;

use App\Repositories\RepositoryInterfaces\EloquentRepositoryInterface as RepositoryInterface;
use App\Models\User;

class UserRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(User $user){
        parent::__construct($user);
        $this->user = $user;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->user;
    }

    public function makeRefrerrerAPArent($ref_id,$child_id,$level_id){
        DB::transaction(function() use ($ref_id,$child_id,$level_id){
            $query_1 = DB::table('children')->select('id')->where('parent_id',$this->ref_id)->where('level_id',$this->level_id)->get(); //Children::where('parent_id',$ref_id)->get(); //"select parent_id from children where parent_id=$ref_id and level_id=$level_id";
            if($query_1->count() < 2 && !$this->checkDuplicateChildId($this->child_id,$this->level_id)) {
                $query = DB::table('children')->insert([
                    'child_id'=>$this->child_id,
                    'parent_id'=>$this->ref_id,
                    'level_id'=>$this->level_id
                ]);//"insert into children (child_id,parent_id,level_id) values('$child_id','$ref_id','$level_id')");
                if(!is_null($this->checkIfRefHasAParent($this->ref_id)) ){
                    $grandparent_id = $this->checkIfRefHasAParent($this->ref_id);
                    $parent_id = $this->ref_id;
                    $grandchild_id = $this->child_id;
                    // make the registrant// a grand child and make the ref a grand parent
                    $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $this->level_id); 
                    //make registrant a great_grandchild
                }
                if($this->level_id == 0){
                    if(!is_null($this->checkIfRefHasAGrandParent($this->ref_id))){
                        $grandparent_id = $this->checkIfRefHasAGrandParent($this->ref_id);
                        if(!is_null($this->checkIfRefHasAParent($grandparent_id))){
                            $great_grandparent_id = $this->checkIfRefHasAParent($grandparent_id);
                            $great_grandchild_id = $grandchild_id;
                            $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $this->level_id);
                        }  
                    }
                } 
            }
            if($query_1->count() == 2 && !$this->checkDuplicateChildId($this->child_id,$this->level_id)) {
                $children = $this->findChildren($this->ref_id,$this->level_id);
                if($children != null){
                    foreach ($children as $value){
                        $value = $value->child_id;
                        $query_2 = DB::table('children')->select('parent_id')->where(['parent_id'=>$value,'level_id'=>$this->level_id])->get();
                        if($query_2->count() < 2 && !$this->checkDuplicateChildId($this->child_id,$this->level_id)) {
                            $query = DB::table('children')->insert([
                                'child_id'=>$this->child_id,
                                'parent_id'=>$value,
                                'level_id'=>$this->level_id
                            ]); //("insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')"); //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                            $grandchild_id = $this->child_id;
                            $parent_id = $value;
                            $grandparent_id = $this->ref_id;
                            $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $this->level_id);
                            if($this->level_id==0){
                                if(!is_null($this->checkIfRefHasAGrandParent($this->ref_id))){
                                    $great_grandparent_id = $this->checkIfRefHasAGrandParent($this->ref_id);
                                    $great_grandchild_id = $grandchild_id;
                                    $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $this->level_id);     
                                }
                            }
                            break;
                        }
                    }
                    if($this->level_id==0){
                        $grandchildren = $this->findGrandChildren($this->ref_id,$this->level_id);
                        if(!is_null($grandchildren)){
                            foreach ($grandchildren as $value){
                                $value = $value->grandchild_id;
                                $query = DB::table('children')->select('parent_id')->where(['parent_id'=>$value,'level_id'=>$this->level_id])->get();//"select parent_id from children where parent_id=$value and level_id=$level_id";
                                if($query->count() < 2 && !$this->checkDuplicateChildId($this->child_id,$this->level_id)) {
                                    $query = DB::table('children')->insert([
                                        'child_id'=>$this->child_id,
                                        'parent_id'=>$value,
                                        'level_id'=>$this->level_id
                                    ]); //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                                    if(!is_null($this->checkIfRefHasAParent($value)) ){
                                        $grandparent_id = $this->checkIfRefHasAParent($value);
                                        $parent_id = $value;
                                        $grandchild_id = $this->child_id;
                                        // make the registrant// a grand child and make the ref a grand parent
                                        $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $this->level_id); 
                                        //make registrant a great_grandchild
                                    }
                                    $great_grandchild_id = $this->child_id;
                                    $parent_id = $value;
                                    $great_grandparent_id = $this->ref_id;
                                    $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $this->level_id);
                                    break;
                                }
                            }
                            $great_grandchildren = $this->findGreatGrandChildren($this->ref_id,$this->level_id);
                            if(!is_null($great_grandchildren)){
                                foreach ($great_grandchildren as $value){
                                    $value = $value->great_grandchild_id;
                                    $query = DB::table('children')->select('parent_id')->where(['parent_id'=>$value,'level_id'=>$this->level_id])->get();
                                    if($query->count() < 2 && !$this->checkDuplicateChildId($this->child_id,$this->level_id)) {
                                        $query = DB::table('children')->insert([
                                            'child_id'=>$this->child_id,
                                            'parent_id'=>$value,
                                            'level_id'=>$this->level_id
                                        ]); //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                                        if(!is_null($this->checkIfRefHasAParent($value)) ){
                                            $grandparent_id = $this->checkIfRefHasAParent($value);
                                            $parent_id = $value;
                                            $grandchild_id = $this->child_id;
                                            // make the registrant// a grand child and make the ref a grand parent
                                            $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id, $this->level_id); 
                                            //make registrant a great_grandchild
                                            if($this->checkIfRefHasAParent( $grandparent_id)){
                                                $great_grandchild_id = $this->child_id;
                                                $parent_id = $value;
                                                $great_grandparent_id = $this->checkIfRefHasAParent( $grandparent_id);
                                                $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $this->level_id);
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

}