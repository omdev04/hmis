<script id="billItemTemplate" type="text/x-jsrender">
<tr>
    <td class="text-center item-number">1</td>
    <td class="table__item-desc">
        <input class="form-control itemName" required="" name="item_name[]" type="text">
    </td>
    <td class="table__qty">
        <input class="form-control qty quantity" required="" name="qty[]" type="number">
    </td>
    <td>
        <input class="form-control price-input price" required="" name="price[]" type="text">
    </td>
    <td class="amount text-right itemTotal">
    </td>
    <td class="text-center">
        <i class="fa fa-trash text-danger delete-bill-bulk-item pointer"></i>
    </td>
</tr>


</script>
