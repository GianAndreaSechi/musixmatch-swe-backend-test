<html>
    <head></head>
    <body>
        <div>
            <h1>Example api call</h1>
            <div id="result">
                <h2>All customer for March</h2>
                <table id="tableAll">
                    <tr>
                        <th>User Id</th>
                        <th>Username</th> 
                        <th>Month</th>
                        <th>Amount</th>
                    </tr>
                </table>
                <hr>
                <h2>Single customer for March</h2>
                <table id="tableSingle">
                    <tr>
                        <th>User Id</th>
                        <th>Username</th> 
                        <th>Month</th>
                        <th>Amount</th>
                    </tr>
                </table>
            </div>
        </div>
        <script>
        fetch('http://localhost/musixmatch/api/index.php/customer/amountDue?month=3')
        .then(response => response.json())
        .then(data => {
            //document.getElementById("result").innerHTML = data;
            data.forEach(element => {
                $row = "<tr>"
                $row = $row + "<td>" + element['user_id'] + "</td>";
                $row = $row + "<td>" + element['username'] + "</td>";
                $row = $row + "<td>" + element['month'] + "</td>";
                $row = $row + "<td>" + element['total_cost'] + "</td>"; 
                $row = $row + "</tr>";
                document.getElementById("tableAll").innerHTML = document.getElementById("tableAll").innerHTML + $row;
            });
        });
        fetch('http://localhost/musixmatch/api/index.php/customer/amountDue?month=3&customer_id=1')
        .then(response => response.json())
        .then(data => {
            //document.getElementById("result").innerHTML = data;
            data.forEach(element => {
                $row = "<tr>"
                $row = $row + "<td>" + element['user_id'] + "</td>";
                $row = $row + "<td>" + element['username'] + "</td>";
                $row = $row + "<td>" + element['month'] + "</td>";
                $row = $row + "<td>" + element['total_cost'] + "</td>"; 
                $row = $row + "</tr>";
                document.getElementById("tableSingle").innerHTML = document.getElementById("tableSingle").innerHTML + $row;
            });
        });
    </script>
    </body>
</html>