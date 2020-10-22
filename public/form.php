<body class="text-center">

    <form class="form-upload" action="/form-handler.php" method="post" enctype="multipart/form-data">

        <img src="https://uploads-ssl.webflow.com/5d30e6ed779cc86e5454596e/5d30ec774a82c65174e55492_leadpilot_color.svg"
             height="24" width="150" alt="logo" />

        <h1 class="h3 mb-3 font-weight-normal">Please fill the form</h1>
        <label for="inputEmail" class="sr-only">Email</label>
        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required
               autofocus/>
        <label for="inputName" class="sr-only">Name</label>
        <input type="text" name="name" id="inputName" class="form-control" placeholder="Enter your full name" required/>

        <label for="inputCsv" class="sr-only">CSV file</label>
        <input type="file" name="csv_file" id="inputCsv" class="form-control" placeholder="Choose a csv file"
               required/>

        <button class="btn btn-lg btn-primary btn-block" type="submit">Upload</button>
    </form>

</body>