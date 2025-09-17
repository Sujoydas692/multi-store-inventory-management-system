<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-5 py-5">
                <div class="row justify-content-between ">
                    <div class="align-items-center col">
                        <h4>Product</h4>
                    </div>
                    <div class="align-items-center col">
                        <button data-bs-toggle="modal" data-bs-target="#create-modal"
                            class="float-end btn m-0  bg-gradient-primary">Create</button>
                    </div>
                </div>
                <hr class="bg-dark " />
                <div id="product" class="modal-body p-3">
                    <table class="table" id="tableData">
                        <thead>
                            <tr class="bg-light">
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Unit</th>
                                <th>Stock</th>
                                <th>Create Date</th>
                                <th>Update Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableList">

                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button onclick="PrintPage()" class="btn bg-gradient-success px-3">
                        Print
                    </button>
                    <button onclick="ProductReport()" class="btn btn-primary px-3">
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    const user = JSON.parse(localStorage.getItem("user"));
    const token = localStorage.getItem("token");

    document.addEventListener('DOMContentLoaded', () => {
        getList();
    });

    async function getList() {


        showLoader();
        let res = await axios.get("/backend/all-product", {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        hideLoader();

        let tableList = $("#tableList");
        let tableData = $("#tableData");

        tableData.DataTable().destroy();
        tableList.empty();

        res.data.data.forEach(function (item, index) {
            let row = `<tr>
                    <td><img class="w-50 h-auto" alt="" id="productImage" src="/storage/${item['img_url']}"></td>
                    <td>${item['name']}</td>
                    <td>${item['price']}</td>
                    <td>${item['unit']}</td>
                    <td>${item['stock_qty']}</td>
                    <td>${formatDate(item['created_at'])}</td>
                    <td>${formatDate(item['updated_at'])}</td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            <div class="btn-group" role="group">
                                <button data-path="/storage/${item['img_url']}" data-id="${item['id']}" class="btn editBtn btn-sm btn-info">Edit</button>
                                <button data-path="/storage/${item['img_url']}" data-id="${item['id']}" class="btn deleteBtn btn-sm btn-primary">Delete</button>
                            </div>
                        </div>
                    </td>
                 </tr>`
            tableList.append(row)
        })

        $('.editBtn').on('click', async function () {
            let id = $(this).data('id');
            let filePath = $(this).data('path');
            await FillUpUpdateForm(id, filePath);
            $("#update-modal").modal('show');
        })

        $('.deleteBtn').on('click', function () {
            let id = $(this).data('id');
            let path = $(this).data('path');

            $("#delete-modal").modal('show');
            $("#deleteID").val(id);
            $("#deleteFilePath").val(path);

        })

        new DataTable('#tableData', {
            order: [[0, 'desc']],
        });

    }

    function PrintPage() {
        let rows = document.querySelectorAll("#tableList tr");
        let printTable = `
        <html>
        <head>
            <title>Product Report</title>
            <style>
                .products {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 12px !important;
        }

        .products td, #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .products tr:nth-child(even){background-color: #f2f2f2;}

        .products tr:hover {background-color: #ddd;}

        .products th {
            padding-top: 12px;
            padding-bottom: 12px;
            padding-left: 6px;
            text-align: left;
            background-color: #04aa6d27;
            color: rgb(0, 0, 0);
        }
            </style>
        </head>
        <table class="products" border="1" cellspacing="0" cellpadding="5" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock Qty</th>
                    <th>Create Date</th>
                </tr>
            </thead>
            <tbody>
    `;

        rows.forEach(row => {
            let name = row.cells[1].innerText;
            let price = row.cells[2].innerText;
            let stock = row.cells[4].innerText;
            let createdAt = row.cells[5] ? row.cells[5].innerText : "";

            if (createdAt) {
                let date = new Date(createdAt);
                createdAt = date.toLocaleString('en-GB', {
                    day: '2-digit', month: 'short', year: 'numeric',
                    hour: '2-digit', minute: '2-digit', hour12: true
                });
            }

            printTable += `
            <tr>
                <td>${name}</td>
                <td>${price}</td>
                <td>${stock}</td>
                <td>${createdAt}</td>
            </tr>
        `;
        });

        printTable += `
        </tbody>
            </table>
        </body>
        </html>`;

        // open print window
        let printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write(printTable);
        printWindow.document.close();
        printWindow.print();
    }

    function ProductReport() {
        window.open('/backend/products-report');
    }

    function formatDate(dateString) {
        let date = new Date(dateString);
        return date.toLocaleString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', hour12: true
        });
    }



</script>