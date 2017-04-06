<?php
//Desenvolvido por Luciano Zanita - WHMCS.RED
//Capturando Session
use WHMCS\Session;
//Laravel DataBase
use WHMCS\Database\Capsule;
//Bloqueia o acesso direto ao arquivo
if (!defined("WHMCS")){
	die("Acesso restrito!");
}
	//Cria o Hook
	function input_red($vars) {
		//Cria o javascript
		$bloqueiojavascript = "<script type='text/javascript'> ";
			//Verifica se é um usuario logado, caso não for ele não exibe nada.
			if($_SESSION["uid"]==''){}
			//Caso estiver logado libera o script
			else{
				//Caso for adicionar um novo contato ele libera o bloqueio
				if($_GET['action']=='addcontact'){}
				//Caso nao for continua.
				else{
					//Verifica se não é subconta, caso for libera a edição
					if($_GET['action']=='contacts'){}
					//Caso não for continua o bloqueio
					else{
						//Faz a listagem de todos que forem para bloquear
						foreach (Capsule::table('mod_inputred')->get() as $mod){
							//Abre o campo para dividir o resultado do nome
							$campo = explode('|', $mod->campo);
							$bloqueiojavascript .= "$('#".$campo[0]."').prop('readOnly', true); ";
						}
					}
				}
			}
		//Finaliza o javascript
		$bloqueiojavascript .= "</script>";

		//Returna o Javascript
		return $bloqueiojavascript;
	}
	//Adicionando o hook a página de editar o cadastro.
	add_hook("ClientAreaFooterOutput",1,"input_red");
?>

