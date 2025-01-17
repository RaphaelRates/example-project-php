<?php

namespace NodacWeb\Controllers;

use NodacWeb\Core\Controller;
use NodacWeb\Controllers\AlertController;
use NodacWeb\Core\View; 
use NodacWeb\Models\Entity\User;

class HomeController extends Controller{
    
    public static function index($request){
        $user = new User;
        return parent::view('page','Home','Youtubers', [
            'name' => $user->name ?? 0,
            'description' => $user->description ?? 0,
            'site' => $user->site ?? 0,
            'itens' => self::getUsersItens($request) ?? 0,
            'status' => self::getStatus($request) ?? 0
        ]);
    }

    public static function testePython($request){
        $filename = "HTML e CSS para iniciantes.pdf";  // Nome do arquivo para download
        $download_url = 'http://python_container:5000/download?file=' . urlencode($filename); // URL da API Flask

        // Caminho completo do diretório de uploads
        $uploadDir = realpath(__DIR__ . '/../../uploads');
        $file_path = $uploadDir . '/' . $filename;

        // Cria o diretório de uploads se não existir
        // if (!is_dir($uploadDir)) {
        //     mkdir($uploadDir, 0777, true);
        // }

        // // Deleta o arquivo local, se já existir
        // if (file_exists($file_path)) {
        //     unlink($file_path);
        // }

        // Inicializa o cURL para fazer o download
        $ch = curl_init($download_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $file_content = curl_exec($ch);

        if ($file_content !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
            // Salva o conteúdo baixado em um arquivo local
            file_put_contents($file_path, $file_content);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Expires: 0');

            readfile($file_path);
            
            unlink($file_path);
            exit;
        } else {
            echo "Erro ao baixar o arquivo.";
        }

        curl_close($ch);
    }

    public static function about($number = 1, $acao = "nada"){
        return parent::view('page','About','Sobre Nós', [
            'number' => $number,
            'acao' => $acao
        ]);
    }

    public static function contact(){
        return parent::view('page','Contact','Contato');
    }

    /**
     * Método responsável por obter a renderização dos itens dos usuários para a página
     *
     * @return void
     */
    private static function getUsersItens($request){
        $itens = ''; //variavel que guardará as views dos objs
        $user = new User(); // instancia da model User
        $limit = 2; //limites da quantodade de elementos por paginação 

        //Definir as variaveis para a paginação e verificação de parameêtros
        $totalUsers = $user->getTotalUsers() ?? 0;
        $queryParams = $request->getQueryParams();
        $pageAtually = $queryParams['page'] ?? 1;
        $offset = ($pageAtually - 1) * $limit;
        $totalPages = ceil($totalUsers / $limit);
        $results = $user->getUsers($limit, $offset) ?? [];

        if($results){
            foreach ($results as $result) {
                $itens .= View::render("components/itemUser",[
                    'id' => $result['id'],
                    'name' => $result['name'],
                    'site' => $result['site'],
                    'description' => $result['description']
                ]);
            }
            $itens .= View::renderPagination($pageAtually, $totalPages);
        }else{
            $itens .= View::render("components/void",[]);
        }
        
    
        return $itens;
    }


    private static function getStatus($request){
        $queryParams = $request->GetQueryParams();

        if(!isset($queryParams['status'])) return '';

        switch ($queryParams['status']) {
            case 'created':
                return AlertController::getSuccess("Canal criado com sucesso");
                break;
            case 'updated':
                return AlertController::getSuccess("Canal atualizado com sucesso");
                break;
            case 'deleted':
                return AlertController::getSuccess("Canal deletado com sucesso");
                break;
                    

            default:
                # code...
                break;
        }

    }


     /**
     * Método responsável por cadastrar usuários do youtube
     *
     * @param [type] $request
     * @return void
     */
    public static function create($request){
        $postVars = $request->getPostVars();
        $user = new User;

        $user->name = $postVars['name'];
        $user->site = $postVars['site'];
        $user->description = $postVars['description'];
        if($user->cadastrar()){
            $request->getRouter()->redirect('/', ["status" => "created"], [], 200);
        }else{
           throw new \Exception("Problema ao criar o usuário",500);
        }

    }

    public static function getSingleUSer($request,$id){
        $user = new User(); 
        $userEdit = $user->getUserById($id);
        return parent::view('page','SingleUser',$userEdit['name'], [
            'name' => $userEdit['name'],
            'description' => $userEdit['description'],
            'site' => $userEdit['site'],
            'id' => $userEdit['id'],
            'next' => $userEdit['id'] == $user->MaxMinxId('max')? $user->MaxMinxId('min'):$user->getAdjacentUserById($userEdit['id'],'next'),
            'back' => $userEdit['id'] == $user->MaxMinxId('min') ? $user->MaxMinxId('max') : $user->getAdjacentUserById($userEdit['id'],'previous'),
        ]);
    }

    public static function editUser($request, $id) {
        // Obtém o usuário pelo ID
        $user = new User(); 
        $userEdit = $user->getUserById($id);
    
        // Verifica se o retorno de getUserById é um array com as chaves certas
        if (!$userEdit || !(self::isUserValid($userEdit))) {
            // Redireciona se o array não for válido
            $request->getRouter()->redirect('/');
        }
        // Renderiza a view de edição do usuário
        return parent::view('page', 'components/formUpdateUser', 'Youtubers', [
            'name' => $userEdit['name'],
            'description' => $userEdit['description'],
            'site' => $userEdit['site'],
        ]);
    }

    private static function isUserValid($array) {
        $expectedKeys = ['id', 'name', 'site', 'description'];
        return !array_diff_key(array_flip($expectedKeys), $array);
    }


    public static function updateUser($request,$id){
        // Obtém o usuário pelo ID
        $user = new User(); 
        $userEdit = $user->getUserById($id);
    
        // Verifica se o retorno de getUserById é um array com as chaves certas
        if (!$userEdit || !(self::isUserValid($userEdit))) {
            // Redireciona se o array não for válido
            $request->getRouter()->redirect('/');
        }

        $postVars = $request->getPostVars();
        $user->id = $userEdit['id'];
        $user->name = $postVars['name'] ?? $userEdit['name'];
        $user->site = $postVars['site'] ?? $userEdit['site'];
        $user->description = $postVars['description'] ?? $userEdit['description'];
        $user->updateUser();
        
        $request->getRouter()->redirect('/', ["status" => "update"], [], 200);

    }

    /**
     * Método responsável por executar a exclusão de um usuário dependendo do 
     * ID
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public static function deleteUser($request,$id){
        $user = new User(); 
        $userEdit = $user->getUserById($id);
        // Verifica se o retorno de getUserById é um array com as chaves certas
        if (!$userEdit || !(self::isUserValid($userEdit))) {
            // Redireciona se o array não for válido
            $request->getRouter()->redirect('/');
        } 
        $user->id = $userEdit['id'];
        $user->deleteUserById($user->id);
        $request->getRouter()->redirect('/', ["status" => "deleted"], [], 200);

        
    }

    

    
    
}