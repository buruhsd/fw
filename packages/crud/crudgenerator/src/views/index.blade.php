<!DOCTYPE html>
<html>
<body>

<h2>Make new Migration</h2>

<form action="{{route('halo-post')}}" method="POST">
  table name:<br>
  <input type="text" name="table" value="Mickey">
  <br>
  <br><br>
  <input type="submit" value="Submit">
</form> 

<p>If you click the "Submit" button, the form-data will be sent to a page called "/action_page.php".</p>

</body>
</html>
