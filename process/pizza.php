<?php

include_once("conn.php");

$method =$_SERVER["REQUEST_METHOD"];

//RESGATE DOS DADOS
if($method === "GET"){

    $bordasQuery = $conn->query("SELECT * FROM bordas;");

    $bordas = $bordasQuery->fetchAll();

    $massasQuery = $conn->query("SELECT * FROM massas;");

    $massas = $massasQuery->fetchAll();

    $saboresQuery = $conn->query("SELECT * FROM sabores;");

    $sabores = $saboresQuery->fetchAll();

//CRIAÇÃO PEDIDO
}else if($method ==="POST"){

    $data = $_POST;

    $borda = $data["borda"];

    $massa = $data["massa"];

    $sabores = $data["sabores"];

    //Validação sabores máximos

    if(count($sabores) > 3){
        $_SESSION["msg"] = "Selecione no máximo 3 sabores!";

        $_SESSION["status"] = "warning";
    } else{
        
        //Salvar borda e massa na pizza
        $stmt = $conn->prepare("INSERT INTO pizzas(borda_id, massa_id) VALUES (:borda, :massa)");

        //Filtro input
        $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);

        $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

        $stmt->execute();

        // Resgate ultimo id pizza
        $pizzaId = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

        //Repetição até concluir savires
        foreach($sabores as $sabor){
            // Filtro input
            $stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);

            $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);

            $stmt -> execute();
        }   

        // Pedido de pizza
        $stmt = $conn-> prepare("INSERT INTO pedidos (pizza_id, status_id) VALUES (:pizza, :status)");

        //Status sempre em 1 (PRODUÇÃO)

        $statusId = 1;

        //Filtro input
        $stmt->bindParam(":pizza", $pizzaId);

        $stmt->bindParam(":status", $statusId);

        $stmt -> execute();

        //Exibir mensagem de sucesso
        $_SESSION["msg"] = "Pedido realizado com sucesso";
        
        $_SESSION["status"] = "Success";
    }
    //Retorna para home
    header("Location:..");
}
?>
