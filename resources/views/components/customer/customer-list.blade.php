<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-5 py-5">
                <div class="row justify-content-between ">
                    <div class="align-items-center col">
                        <h4>Customer</h4>
                    </div>
                    <div class="align-items-center col">
                        <button data-bs-toggle="modal" data-bs-target="#create-modal"
                            class="float-end btn m-0 bg-gradient-primary">Create</button>
                    </div>
                </div>
                <hr class="bg-dark " />
                <table class="table" id="tableData">
                    <thead>
                        <tr class="bg-light">
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Create Date</th>
                            <th>Update Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableList">

                    </tbody>
                </table>
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
        try {
            let res = await axios.get("/backend/all-customer", {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            });

            hideLoader();
            let customers = Array.isArray(res.data?.data) ? res.data.data : [];
            let tableList = $("#tableList");
            let tableData = $("#tableData");

            if ($.fn.DataTable.isDataTable('#tableData')) {
            tableData.DataTable().destroy();
        }
            tableList.empty();

            customers.forEach(function (item, index) {
                let row = `<tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>${item.email}</td>
                <td>${item.mobile}</td>
                <td>${formatDate(item.created_at)}</td>
                <td>${formatDate(item.updated_at)}</td>
                <td>
                    <div class="d-flex justify-content-end gap-2">
                         <div class="btn-group" role="group">
                            <button data-id="${item.id}" class="float-end btn btn editBtn btn-sm btn-info">Edit</button>
                            <button data-id="${item.id}" class="float-end btn btn deleteBtn btn-sm btn-primary">Delete</button>
                        </div>
                    </div>
                </td>
            </tr>`
                tableList.append(row)
            })

            $('#tableList').on('click', '.editBtn', async function () {
                let id = $(this).data('id');
                await FillUpUpdateForm(id);
                $("#update-modal").modal('show');
            });

            $('#tableList').on('click', '.deleteBtn', async function () {
                let id = $(this).data('id');
                await ShowName(id);
                $("#delete-modal").modal('show');
                $("#deleteID").val(id);
            });

            new DataTable('#tableData', {
                order: [[0, 'asc']],
                // lengthMenu:[5,10,15,20,30]
            });

        } catch (err) {
            if (err.response && err.response.status === 500) {
                errorToast(err.response.data.message);
            } else {
                errorToast(err.response.data.message);
            }
        }

    }

    function formatDate(dateString) {
        let date = new Date(dateString);
        return date.toLocaleString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', hour12: true
        });
    }


</script>