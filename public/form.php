<form class="form-upload" action="/form-handler.php" method="post" enctype="multipart/form-data">
    <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72"
         height="72">
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