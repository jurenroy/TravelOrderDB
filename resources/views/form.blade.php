<form method="post" action="{{ route('submit.form') }}" enctype="multipart/form-data">
    @csrf <!-- CSRF token for security -->
    <label for="name_id">Name ID:</label>
    <input type="text" name="name_id" id="name_id"><br>
    
    <label for="position_id">Position ID:</label>
    <input type="text" name="position_id" id="position_id"><br>

    <label for="division_id">Division ID:</label>
    <input type="text" name="division_id" id="division_id"><br>

    <label for="station">Station:</label>
    <input type="text" name="station" id="station"><br>

    <label for="destination">Destination:</label>
    <input type="text" name="destination" id="destination"><br>

    <label for="purpose">Purpose:</label>
    <input type="text" name="purpose" id="purpose"><br>

    <label for="departure">Departure:</label>
    <input type="text" name="departure" id="departure"><br>

    <label for="arrival">Arrival:</label>
    <input type="text" name="arrival" id="arrival"><br>

    <label for="signature1">Signature 1:</label>
    <input type="file" name="signature1" id="signature1"><br>

    <label for="signature2">Signature 2:</label>
    <input type="file" name="signature2" id="signature2"><br>

    <label for="pdea">PDEA:</label>
    <input type="text" name="pdea" id="pdea"><br>

    <label for="ala">ALA:</label>
    <input type="text" name="ala" id="ala"><br>

    <label for="appropriations">Appropriations:</label>
    <input type="text" name="appropriations" id="appropriations"><br>

    <label for="remarks">Remarks:</label>
    <input type="text" name="remarks" id="remarks"><br>
    
    <!-- Add input fields for other attributes as needed -->

    <button type="submit">Submit</button>
</form>
