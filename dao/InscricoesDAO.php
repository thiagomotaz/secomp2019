<?php

    require_once('../BancoDeDados/database.php');
    
    class InscricoesDAO {

        private $conn;

        public function __construct() {
            $database = new Database();
            $db = $database->dbConnection();
            $this->conn = $db;
        }

        public function runQuery($sql) {
            $stmt = $this->conn->prepare($sql);
            return $stmt;
        }

        public function addInscricaoMinicurso(Inscricoes $Inscricoes) {
            try {

                $vagasRestantes = 0;    
                $id = $Inscricoes->getId();
                $usuario = $Inscricoes->getUsuario();
                $data = $Inscricoes->getData();
                $tipo = $Inscricoes->getTipo();
                $stmtVagasMinicursos = $this->conn->prepare("SELECT * FROM minicursos WHERE codMinicurso = '".$id."'");
                $stmtVagasMinicursos->execute();
                $RowVagasMinicursos = $stmtVagasMinicursos->fetch(PDO::FETCH_ASSOC);

                $stmtInscricoes = $this->conn->prepare("SELECT * FROM inscricoes WHERE codMinicurso = '".$id."'");
                $stmtInscricoes->execute();

                $stmtInscricoesUsuario = $this->conn->prepare("SELECT * FROM inscricoes WHERE codUsuario = '".$usuario."'");
                $stmtInscricoesUsuario->execute();

                $stmtTipoInscricaoUsuario= $this->conn->prepare("SELECT * FROM inscricoes, minicursos WHERE inscricoes.codUsuario = '".$usuario."' 
                AND minicursos.codMinicurso = inscricoes.codMinicurso AND minicursos.horarioMinicurso = $tipo");
                $stmtTipoInscricaoUsuario->execute();

                //vejo em inscricoes se esse usuario ja tem uma inscricao nesse tipo de horario
                //pego o tipo do que ele ta se inscrevendo e verifico se ele já tem uma nesse tipo
                if($stmtInscricoesUsuario->rowCount() >= 3 ){
                    echo 4;
                }
                else if($stmtTipoInscricaoUsuario->rowCount() > 0){
                    echo 13;
                }
                else{
                    $stmtInscricoes->rowCount();
                    $vagasRestantes = ($RowVagasMinicursos['vagasMinicurso'] - $stmtInscricoes->rowCount());

                    if($vagasRestantes <= 0){
                        echo 3;
                    }else{
                        $stmt = $this->conn->prepare("INSERT INTO  inscricoes(codUsuario, codMinicurso, dataInscricao) VALUES(:codUsuario, :codMinicurso, :dataInscricao)");

                        $stmt->bindparam(":codMinicurso", $id);
                        $stmt->bindparam(":codUsuario", $usuario);
                        $stmt->bindparam(":dataInscricao", $data);
                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            echo 1;
                        } else {
                            echo 2;
                        }
                    }

                }

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function calcelarInscricaoMinicurso(Inscricoes $Inscricoes) {
            try {

                $usuario = $Inscricoes->getUsuario();
                $id = $Inscricoes->getId();
                
                $stmt = $this->conn->prepare("DELETE FROM inscricoes WHERE codUsuario = ? AND codMinicurso = ?");

                $stmt->bindparam(1, $usuario);
                $stmt->bindparam(2, $id);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    echo 1;
                } else {
                    echo 2;
                }

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

    }

?>