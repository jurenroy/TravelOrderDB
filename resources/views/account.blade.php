<form method="post" action="{{ route('submit.form') }}" enctype="multipart/form-data">
    @csrf <!-- CSRF token for security -->
    
    <label for="type_id">Type ID:</label>
    <input type="text" name="type_id" id="type_id"><br>

    <label for="name_id">Name ID:</label>
    <input type="text" name="name_id" id="name_id"><br>

    <label for="email">Email:</label>
    <input type="text" name="email" id="email"><br>

    <label for="password">Password:</label>
    <input type="text" name="password" id="password"><br>

    
    <!-- Add input fields for other attributes as needed -->

    <button type="submit">Submit</button>
</form>
