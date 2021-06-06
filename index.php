<?php
    error_reporting(0);
    //start session
    session_start();
    //initialize session shopping cart
    if (!isset($_SESSION['cart']))
    {
        $_SESSION['cart']=array();
    }
    //look for catalog file
    $catalogFile="catalog.dat";
    //file is available, extract data from it
    //place into $CATALOG array, with SKU as key
    if (file_exists($catalogFile))
    {
        $data=file($catalogFile);
        foreach ($data as $line)
        {
            $lineArray=explode(':', $line);
            $sku=trim($lineArray[0]);
            $CATALOG[$sku]['desc']=trim($lineArray[1]);
            $CATALOG[$sku]['price']=trim($lineArray[2]);
        }
    }
    else
    {
        die("Could not find catalog file");
    }
    //check to see if the form has been submitted
    //and which submit button was clicked
    //if this is an add operation
    //add to already existing quantities in shopping cart
    if ($_POST['add'])
    {
        foreach ($_POST['a_qty'] as $k=>$v)
        {
            //if the value is 0 or negative
            //don't bother changing the cart
            if ($v>0)
            {
                $_SESSION['cart'][$k]=$_SESSION['cart'][$k] + $v;
            }
        }
    }
    //if this is an update operation
    //replace quantites in shopping cart with values entered
    else if ($_POST['update'])
    {
        foreach ($_POST['u_qty'] as $k=>$v)
        {
            //if the value is empty, 0 or negative
            //don't bother changing the cart
            if ($v!=" " && $v>=0)
            {
                $_SESSION['cart'][$k]=$v;
            }
        }
    }
    //if this is clear operation
    //reset the session and the cart
    //destroy all session data
    else if ($_POST['clear'])
    {
        $_SESSION=array();
        session_destroy();
    }
?>
<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@5.8.55/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="styles.css">
        <title>Cathalog</title>
    </head>
    
    <body class="p-3 mb-2 bg-dark text-white">
        <form class="text-center">
            <blockquote class="blockquote">
                <h1 class="display-5"></h1>
            </blockquote>
        Please add items from the list below to your shopping cart.
        <div class = "mid">
            <form action="<?$_SERVER['PHP_SELF']?>" method="post">
                <table border="0" cellspacing="10">
                    <?php
                        //print items from the catalog for selection
                        foreach ($CATALOG as $k=>$v)
                        {
                            echo "<tr><td colspan=2>";
                            echo "<b>".$v['desc']."</b>";
                            echo "</td></tr>\n";
                            echo "<tr><td>";
                            echo "Price per unit: ".$CATALOG[$k]['price'];
                            echo "<tr><td>";echo "<tr><td>";echo "<tr><td>";
                            echo "</td><td><td>Quantity: ";
                            echo "<input size=10 type=text name=\"a_qty[" .$k. "]\">";
                            echo "</td></tr>\n";
                        }
                    ?>
                    <tr>
                        <td colspan="2">
                            <button type="add" name="add" class="btn btn-outline-primary">Add items to cart</button>
                        </td>
                    </tr>
                </table>
                </div>
            <hr/><hr/>
            <h2 class="display-6">Shopping Cart</h2>
            <table width="100%" border="0" cellspacing="10">
                <?php
                    //initialize a variable to hold total cost
                    $total=0;
                    //check the shopping cart
                    //if it contains values
                    //look up the SKUs in the $CATALOG array
                    //get the cost and calculate subtotals and totals
                    if (is_array($_SESSION['cart']))
                    {
                        foreach ($_SESSION['cart'] as $k=>$v)
                        {
                            //only display items that have been selected
                            //that is, quantities>0
                            if ($v>0)
                            {
                                $subtotal=$v*$CATALOG[$k]['price'];
                                $total+=$subtotal;
                                echo"<tr><td>";
                                echo"<b>$v unit(s) of " . $CATALOG[$k]['desc']."</b>";
                                echo"</td><td>";
                                echo "New quantity: <input size=4 type=text name=\"u_qty[" . $k . "]\">";
                                echo "</td></tr>\n";
                                echo "<tr><td>";
                                echo "Price per unit: " . $CATALOG[$k]['price'];
                                echo "</td><td>";
                                echo "Sub-total: " . sprintf("%0.2f", $subtotal);
                                echo "</td></tr>\n";
                            }
                        }
                    }
                ?>
                <tr>
                    <td class = "low"><b>TOTAL</b></td>
                    <td><b><?=sprintf("%.2f", $total)?></b></td>
                </tr>
                <tr>
                    <td class = "low"><button type="submit" name="update" class="btn btn-outline-primary">Update Cart</button></td>
                    <td><button type="submit" name="clear" class="btn btn-outline-primary">Clear Cart</button></td>
                </tr>
            </table>
        </form>
    </body>
</html>
