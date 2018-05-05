<?php


namespace controllers;


use app\Accessor;
use app\Core;
use app\Utils;
use models\Blocked;
use models\Category;
use models\Snippets;
use models\Subcategory;
use models\Users;
use models\Visits;
use widgets\Detected;

class Handler extends Base
{
    private $permission;
    /** @var  Category */
    private $modelCategory;
    /** @var  Subcategory */
    private $modelSubcategory;
    /** @var  Snippets */
    private $modelSnippets;

    public function init() {
        parent::init();
        $this->modelCategory = new Category();
        $this->modelSubcategory = new Subcategory();
        $this->modelSnippets = new Snippets();
        $this->permission = ($this->isAuth()) ? $this->authData['permission'] : 0;
    }


    public function index($action, $one=null, $two=null, $three=null)
    {
        if(method_exists($this, $action))
        {
            $this->$action($one, $two, $three);
        }
        else {

            $modelSnippets = new Snippets();
            $modelSubcat = new Subcategory();

            $listSubcat = $modelSubcat->getSubcategoryList($this->permission, $action);
            $listSnippets = $modelSnippets->getSnippetsList($this->permission, $action, $one, $two);

            $info['category'] = $this->currentCategory($action);
            $info['subcategory'] = $this->currentSubcategory($one);
            $info['snippets'] =  $this->currentSnippet($two);


            # Просмотр снипета
            if($two && $listSnippets) {

                $this->lookedAdd($info['snippets']['id']);

                $this->metaTitle($info['snippets']['title']);
                $this->metaKeyword($info['snippets']['metakey'].','.$info['snippets']['tags']);
                $this->metaDescription($info['snippets']['metadesc']);

                $snipData = $this->partial('handler/item.php', [
                    'data'=>$listSnippets[0],
                ]);
            }

            # Список снипетов по категории или по суб-категории
            else {

                # запись мета для списка суб-категорий или категорий
                if($one){
                    $this->metaTitle("Snippets {$info['category']['title']} - {$info['subcategory']['title']}");
                    $this->metaKeyword("".$info['subcategory']['metakey']);
                    $this->metaDescription("".$info['subcategory']['metadesc']);
                    $description = $info['subcategory']['description'];
                } else {
                    $this->metaTitle("Snippets {$info['category']['title']}");
                    $this->metaKeyword("".$info['category']['metakey']);
                    $this->metaDescription("".$info['category']['metadesc']);
                    $description = $info['category']['description'];
                }

                $snipData = $this->partial('handler/items.php',[
                    'desc'=>$description,
                    'data'=>$listSnippets,
                ]);

            }

            $this->render('contents/half', [
                    'column' => $this->partial('handler/cat_left', [
                            'data'=> $listSubcat
                        ]),

                    'content' => $this->partial('handler/cat_right', [
                            'info'=> $info,
                            'data'=> $snipData
                        ]),
                ]);
        }
    }


    public function currentCategory($link)
    {
        if(self::$dataCategory){
            foreach(self::$dataCategory as $dc) {
                if($dc['link'] == $link)
                    return $dc;
            }
        }
        return false;
    }

    public function currentSubcategory($link)
    {
        if(!empty($link) && $result = Subcategory::model()->getOne('link = ?',$link))
            return $result;
        return false;
    }

    public function currentSnippet($link)
    {
        if(!empty($link) && $result = Snippets::model()->getOne('link = ?',$link))
            return $result;
        return false;
    }




    /**
     * Формирует правила для роутера
     * @return string
     */
    public static function registeredRegExp()
    {
        if(self::$dataCategory == null){
            self::initCommonData();
        }

        $cond = 'none';
        foreach(self::$dataCategory as $cat)
            $cond .= '|'.$cat['link'];

        return $cond;
    }


    # snippet
    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    public function snippet($link)
    {
        $snippet = $this->currentSnippet($link);
        if(!$snippet)
            $this->notFound();

        $this->lookedAdd($snippet['id']);
        $this->metaTitle($snippet['title']);
        $this->metaKeyword($snippet['metakey'].','.$snippet['tags']);
        $this->metaDescription($snippet['metadesc']);

        $subCat = Subcategory::model()->getOneById($snippet['idsubcategory']);
        $cat = Category::model()->getOneById($subCat['idcategory']);
        $user = Users::model()->getOneById($snippet['iduser']);

        $links['category'] = '<a href="'."/{$cat['link']}".'">'.$cat['title'].'</a>';
        $links['subcategory'] = '<a href="'."/{$cat['link']}/{$subCat['link']}".'">'.$subCat['title'].'</a>';
        $links['snippet'] = '<a href="'."/{$cat['link']}/{$subCat['link']}/{$snippet['link']}".'">'.$snippet['title'].'</a>';

        $snippetView = $this->partial('handler/snippet.php', [
            'dataSnippet'=>$snippet,
            'dataSubCat'=>$subCat,
            'dataCat'=>$cat,
            'dataUser'=>$user,
            'links'=>$links,
        ]);

        $this->render('contents/full',[
            'content'=>$snippetView
        ]);
    }



    # edit create categories and subcategories
    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    /**
     * @param null $idCat
     * @param null $idSubcat
     * @param null $idSnipp
     */
    public function settings($idCat=null, $idSubcat=null, $idSnipp=null)
    {
        if(!$this->isAuth())
            $this->notFound();

        $this->view->addValue('currentUrl',Core::Request()->getResourceUri());

        # SAVE DATA.
        if($form_type = Accessor::post('form_type')) {
            $this->saveData($form_type, $idCat);
        }

        # VIEW DATA

        $column = $content = null;

        $selectCat = false;
        $selectSubcat = false;
        $selectSnippet = false;

        $listSubcatByCatId = false;
        $listSnippetsBySubcatId = false;


        # Данные колонк таблиц category
        $dataCatDefault = $this->modelCategory->columns();
        $dataCatDefault['link'] = 'c'.( (int) $this->modelCategory->lastId() + 1);

        $dataSubcatDefault = $this->modelSubcategory->columns();
        $dataSubcatDefault['link'] = 'sc'.( (int) $this->modelSubcategory->lastId() + 1);

        $dataSnippetsDefault = $this->modelSnippets->columns();
        $dataSnippetsDefault['link'] = 's'.( (int) $this->modelSnippets->lastId() + 1);


        # Все данные c таблицы category
        //Заменяю ->getAll();
        $allCat = $this->modelCategory->getAll("permission <= ? ORDER BY ordering", [$this->permission]);
        $allSubcat = $this->modelSubcategory->getAll("permission <= ? ORDER BY ordering", [$this->permission]);
        $allSnip = $this->modelSnippets->getAll("permission <= ? ORDER BY ordering", [$this->permission]);


        # ...
        if(is_numeric($idCat))
            $selectCat = $this->modelCategory->getOneById($idCat);
        # ...
        if(is_numeric($idSubcat))
            $selectSubcat = $this->modelSubcategory->getOneById($idSubcat);
        # ...
        if(is_numeric($idSnipp))
            $selectSnippet = $this->modelSnippets->getOneById($idSnipp);

        if(is_numeric($idCat))
            $listSubcatByCatId = $this->modelSubcategory->getAll("idcategory = ? ORDER BY ordering", [$idCat]);
            //$listSubcatByCatId = $this->modelSubcategory->getAllByAttr('idcategory', $idCat);

        if(is_numeric($idSubcat))
            $listSnippetsBySubcatId = $this->modelSnippets->getAll("idsubcategory = ? ORDER BY ordering", [$idSubcat]);
            //$listSnippetsBySubcatId = $this->modelSnippets->getAllByAttr('idsubcategory', $idSubcat);


# показать список катеорий и создание новой
        if(empty($idCat) || $idCat == 'new') {

            $this->metaTitle("Create new Category");

            # лист всех категорий
            $column = $this->partial('handler/edit_list', [
                'allCat' => $allCat
            ]);

            # форма редактирования
            $content = $this->partial('handler/cat_edit_form', [
                'data' => $dataCatDefault,
            ]);

        }
# показать субкатегории, редактирование категорий
        elseif(is_numeric($idCat) && empty($idSubcat)) {

            $this->metaTitle("Edit Category");

            if(empty($selectCat))
                $this->redirect('/settings');

            $column = $this->partial('handler/edit_list', [
                'allCat' => $allCat,
                'listSubcat' => $listSubcatByCatId,
                'idCat' => $idCat,
            ]);

            $content = $this->partial('handler/cat_edit_form', [
                'data' => $selectCat,
                'idCat' => $idCat,
            ]);

        }
# создание новой субкатегории
        elseif(is_numeric($idCat) && $idSubcat == 'new'){

            $this->metaTitle("Create new SubCategory");

            $column = $this->partial('handler/edit_list', [
                'allCat' => $allCat,
                'listSubcat' => $listSubcatByCatId,
                'idCat' => $idCat,
            ]);

            $content = $this->partial('handler/scat_edit_form', [
                'data' => $dataSubcatDefault,
                'allCat' => $allCat,
                'idCat' => $idCat
            ]);

        }
# показать снипеты, редактирование субкатегорий
        elseif(!empty($idCat) && is_numeric($idSubcat) && empty($idSnipp)){

            $this->metaTitle("Edit SubCategory");

            $column = $this->partial('handler/edit_list', [
                'allCat' => $allCat,
                'listSubcat' => $listSubcatByCatId,
                'listSnippets' => $listSnippetsBySubcatId,
                'idCat' => $idCat,
                'idSubcat' => $idSubcat,
            ]);

            $content = $this->partial('handler/scat_edit_form', [
                'data' => $selectSubcat,
                'allCat' => $allCat,
                'idCat' => $idCat,
                'idSubcat' => $idSubcat,
            ]);

        }
# новый снипет
        elseif(is_numeric($idSubcat) && $idSnipp == 'new'){

            $this->metaTitle("Create new Snippet");

            $column = $this->partial('handler/edit_list', [
                'allCat' => $allCat,
                'listSubcat' => $listSubcatByCatId,
                'listSnippets' => $listSnippetsBySubcatId,
                'idCat' => $idCat,
                'idSubcat' => $idSubcat,
            ]);

            $content = $this->partial('handler/snippet_edit_form', [
                'allCat' => $allCat,
                'allSubcat' => $allSubcat,
                'data' => $dataSnippetsDefault,
                'listSubcat' => $listSubcatByCatId,
                'idCat' => $idCat,
                'idSubcat' => $idSubcat,
            ]);

        }
# редактирование снипета
        elseif(is_numeric($idSubcat) && is_numeric($idSnipp)){

            $this->metaTitle("Edit Snippet");
            $linkView = false;
            if(isset($selectSnippet['link'])){
                $linkView = "/{$selectCat['link']}/{$selectSubcat['link']}/{$selectSnippet['link']}";
            }

            $column = $this->partial('handler/edit_list', [
                'allCat' => $allCat,
                'listSubcat' => $listSubcatByCatId,
                'listSnippets' => $listSnippetsBySubcatId,
                'idCat' => $idCat,
                'idSubcat' => $idSubcat,
                'idSnipp' => $idSnipp,
            ]);

            $content = $this->partial('handler/snippet_edit_form', [
                'allCat' => $allCat,
                'allSubcat' => $allSubcat,
                'data' => $selectSnippet,
                'listSubcat' => $listSubcatByCatId,
                'idCat' => $idCat,
                'idSubcat' => $idSubcat,
                'idSnipp' => $idSnipp,
                'linkView' => $linkView,
            ]);

        }

        //$this->view->addData('title','Edit Category');

        $this->render('contents/half', [
            'column' => $column,
            'content'=> $content,
        ]);

    }


    /**
     * Work with DB, if not empty POST http request
     *
     * @param $form_type
     * @param $idCat
     */
    private function saveData($form_type, $idCat)
    {

        if($form_type == 'category'){

            $data['metakey'] = Accessor::post('metakey');
            $data['metadesc'] = Accessor::post('metadesc');
            $data['link'] = Accessor::post('link');
            $data['title'] = Accessor::post('title');
            $data['description'] = Accessor::post('description');
            $data['ordering'] = Accessor::post('ordering');
            $data['enabled'] = Accessor::post('enabled');
            $data['permission'] = Accessor::post('permission');
            $data['created'] = date('d.m.Y H:i:s');

            // Update record OR delete
            if($id = Accessor::post('id') AND is_numeric($id)){
                if('Delete' == Accessor::post('delete')){
                    $this->modelCategory->db->delete($this->modelCategory->table,'id = ?', $id);
                    $this->redirect('/settings');
                }
                else {
                    $this->modelCategory->db->update($this->modelCategory->table, $data, 'id = ?', $id);
                    $this->redirect("/settings/$id");
                }

                // Save new record
            }else{
                $insId = $this->modelCategory->db->insert($this->modelCategory->table, $data);
                $this->redirect('/settings/'.$insId);
            }

        } else if($form_type == 'subcategory'){

            $data['idcategory'] = Accessor::post('idcategory');
            $data['metakey'] = Accessor::post('metakey');
            $data['metadesc'] = Accessor::post('metadesc');
            $data['link'] = Accessor::post('link');
            $data['title'] = Accessor::post('title');
            $data['description'] = Accessor::post('description');
            $data['ordering'] = Accessor::post('ordering');
            $data['enabled'] = Accessor::post('enabled');
            $data['permission'] = Accessor::post('permission');
            $data['created'] = date('d.m.Y H:i:s');

            // Update record OR delete
            if($id = Accessor::post('id') AND is_numeric($id)){
                if('Delete' == Accessor::post('delete')){
                    $this->modelSubcategory->db->delete($this->modelSubcategory->table,'id = ?', $id);
                    $this->redirect("/settings/{$data['idcategory']}/");
                }
                else{
                    $this->modelSubcategory->db->update($this->modelSubcategory->table, $data, 'id = ?', $id);
                    $this->redirect("/settings/{$data['idcategory']}/$id");
                }

                // Save new record
            }else{

                $insId = $this->modelSubcategory->db->insert($this->modelSubcategory->table, $data);
                $this->redirect("/settings/{$data['idcategory']}/".$insId);
            }

        } else if($form_type == 'snippet'){

            $idCat = Accessor::post('category');

            $data['iduser'] = $this->authData['id'];
            $data['idsubcategory'] = Accessor::post('idsubcategory');
            $data['metakey'] = Accessor::post('metakey');
            $data['metadesc'] = Accessor::post('metadesc');
            $data['link'] = Accessor::post('link');
            $data['tags'] = Accessor::post('tags');
            $data['vote'] = Accessor::post('vote');
            $data['title'] = Accessor::post('title');
            $data['description'] = htmlspecialchars(Accessor::post('description'));
            $data['content'] = htmlspecialchars(Accessor::post('content'));
            $data['ordering'] = Accessor::post('ordering');
            $data['enabled'] = Accessor::post('enabled');
            $data['permission'] = Accessor::post('permission');
            $data['created'] = date('d.m.Y H:i:s');

            // Update record OR delete
            if($id = Accessor::post('id') AND is_numeric($id)){
                if('Delete' == Accessor::post('delete')){
                    $this->modelSnippets->db->delete($this->modelSnippets->table,'id = ?', $id);
                    $this->redirect("/settings/$idCat/{$data['idsubcategory']}");
                }
                else{
                    $this->modelSnippets->db->update($this->modelSnippets->table, $data, 'id = ?', $id);
                    $this->redirect("/settings/$idCat/{$data['idsubcategory']}/$id");
                }

                // Save new record
            }else{
                $insId = $this->modelSnippets->db->insert($this->modelSnippets->table, $data);
                $this->redirect("/settings/$idCat/{$data['idsubcategory']}/$insId");
            }

        }
    }

    public function search($search)
    {

        if(!$search)
            $search = Accessor::get('search');

        $searchData = [];
        $searchDataReady = [];
        $searchDataInfo = [];

        if($search && strlen($search)>1){
            $resFull = $this->modelSnippets->searchQuery($search);

            if(is_array($resFull))
                $searchData = $resFull;

            if(strpos($search,' ') !== false){
                $searchArr = explode(' ',$search);
                foreach($searchArr as $word){
                    $resWord = $this->modelSnippets->searchQuery(trim($word));

                    if(empty($searchData) && is_array($resWord)) {
                        $searchData = $resWord;
                    }
                    else if(is_array($resWord)) {
                        foreach($resWord as $rw)
                            array_push($searchData, $rw);
                    }

                }
            }
        }

        //filter
        if(!empty($searchData)){
            $c = $sc = [];
            foreach($searchData as $sd){
                $searchDataReady[$sd['id']] = $sd;
                $c[$sd['clink']] = $sd['ctitle'];
                $sc[$sd['clink'].'/'.$sd['sclink']] = $sd['sctitle'];
            }
            $searchDataInfo['length'] = count($searchDataReady);
            $searchDataInfo['category'] = $c;
            $searchDataInfo['subcategory'] = $sc;
        }

        $hasResult = (empty($searchDataReady)) ? 'без результатно' : 'состоялся';
        $this->metaTitle("Поиск: ".$hasResult);
        $this->metaKeyword('поиск кода, найти php, найти кодб решение');

        $this->render('contents/half', [
            'column' => $this->partial('/handler/search_info.php', ['data'=>$searchDataInfo]),
            'content'=> $this->partial('/handler/search_content.php', ['data'=>$searchDataReady,'word'=>$search]),
        ]);

    }

    public function selectvote(){
            $id = Accessor::post('id');
            $value = Accessor::post('value');
            if(!Accessor::cookies('vote-'.$id)){
                Accessor::cookies('vote-'.$id, 'checked', time()+3600*24*7,'/');
                $votePlusOne = (int)$this->modelSnippets->getOne('id = ?', $id)['vote'] += $value;
                $this->modelSnippets->db->update('snippets', ['vote'=>$votePlusOne], 'id = ?', $id);
                echo $votePlusOne;
            }else
                echo 'null';
    }



    public function visitsDetect(){
        // detected
        // http://api.2ip.com.ua/geo.json?ip=176.119.107.54
        // http://api.2ip.com.ua/provider.json?ip=176.119.107.54

        $ip = Accessor::post('ip');
        $id = Accessor::post('id');

        if(filter_var($ip, FILTER_VALIDATE_IP) && is_numeric($id)){
            //print_r($ip);
            $data['geo'] = [];
            $data['prv'] = [];

            $url = "http://api.2ip.com.ua/geo.json?ip=$ip";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpCode == '200'){
                try{
                    $resultArr = json_decode($result ,true);

                    $data['geo']['country'] = $resultArr['country'];
                    $data['geo']['region'] = $resultArr['region'];
                    $data['geo']['city'] = $resultArr['city'];

                }catch (\Exception $e) {}
            }

            $url = "http://api.2ip.com.ua/provider.json?ip=$ip";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpCode == '200'){
                try{
                    $resultArr = json_decode($result ,true);

                    $data['prv']['name_ripe'] = $resultArr['name_ripe'];
                    $data['prv']['site'] = $resultArr['site'];
                    $data['prv']['route'] = $resultArr['route'];

                }catch (\Exception $e) {}

                if(!empty($data)){
                    Visits::model()->db->update('visits', ['detected'=>serialize($data)], 'id = ?', [$id]);
                    echo json_encode($data);
                }
            }

            die;
        }
    }



    public function visits(){

        $data = Visits::model()->db->executeAll('SELECT * from visits ORDER BY lastvisit DESC LIMIT 200 ');
        $result = [];
        for($i=0;$i<count($data);$i++){
            if(!Blocked::agentsBotsFilter($data[$i]['agent']))
                $result[] = $data[$i];
            else
                $data[$i] = false;
        }
        unset ($data);

        $this->render('contents/full',[
            'content'=> $this->partial('handler/visits_list', [
                'data'=> $result
            ]),
        ]);

    }





}