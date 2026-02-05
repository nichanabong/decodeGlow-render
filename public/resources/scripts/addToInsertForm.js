const addToInsertForm = () => {
    const category = document.getElementById("category").value;
    const product_name = document.getElementById("product_name");
    const ingredients_list = document.getElementById("ingredients_list");

    if (category == 3) {
        product_name_div.style.display = "block"
        ingredients_list_div.style.display = "block"

    } else {
        product_name_div.style.display = "none"
        ingredients_list_div.style.display = "none"
    }
}