<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!--suppress JSCheckFunctionSignatures -->
<script>
    $(document).ready(function () {
        $('.search-box input[type="text"]').on("keyup input", function () {
            /* Get input value on change */
	        const inputVal = $(this).val();
	        const resultDropdown = $(this).siblings(".result");
	        if (inputVal.length) {
                $.get("<?php echo relativeURL('shared/snippets/backend-search') ?>", {term: inputVal}).done(function (data) {
	                // Display the returned data in browser
	                resultDropdown.html(data);
                });
	        } else {
		        resultDropdown.empty();
	        }
        });

	    // Set search input value on click of result item
	    $(document).on("click", ".result p", function () {
		    $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
		    $(this).parent(".result").empty();
		    $("#person-select-form").submit();
	    });
    });
</script>

<div class="search-box">
    <!--suppress HtmlFormInputWithoutLabel, XmlInvalidId -->
    <input name="select-id" type="text" autocomplete="off" form="person-select-form" placeholder="Search..." size="25">
    <div class="result"></div>
</div>
