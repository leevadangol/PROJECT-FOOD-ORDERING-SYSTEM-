<?php 
include "a-header.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Manage Food</title>
  <link rel="stylesheet" href="/PROJECT (FOOD ORDERING SYSTEM)/CSS/a-update_style.css" />
</head>

<body>

  <!-- Admin Page Title -->
  <h2 class="page-title">Update Page</h2>

  <!-- Main Container -->
  <section class="admin-container">

    <div class="update-box">
      <div class="update-header">
        <h3>Update :</h3>
        <a href="#" class="btn-add">Add Food</a>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>S.N.</th>
              <th>Title</th>
              <th>Price</th>
              <th>Active Status</th>
              <th>Image</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td>1.</td>
              <td>Pepperonie Pizza</td>
              <td>550</td>
              <td class="active">Active</td>
              <td>
                <img src="/PROJECT (FOOD ORDERING SYSTEM)/IMAGES/Pepperoni Pizza.jpeg" alt="pizza">
              </td>
              <td>
                <a href="#" class="btn-update">Update</a>
                <a href="#" class="btn-delete">Delete</a>
              </td>
            </tr>

            <tr>
              <td>2.</td>
              <td>Fried Mo:Mo</td>
              <td>550</td>
              <td class="not-active">Not Active</td>
              <td>
                <img src="/PROJECT (FOOD ORDERING SYSTEM)/IMAGES/momo.jpg" alt="fried Mo:Mo">
              </td>
              <td>
                <a href="#" class="btn-update">Update</a>
                <a href="#" class="btn-delete">Delete</a>
              </td>
            </tr>

            <tr>
              <td>3.</td>
              <td>Fried Chicken</td>
              <td>550</td>
              <td class="not-active">Not Active</td>
              <td>
                <img src="/PROJECT (FOOD ORDERING SYSTEM)/IMAGES/Fried chicken.jpg" alt="fried chicken">
              </td>
              <td>
                <a href="#" class="btn-update">Update</a>
                <a href="#" class="btn-delete">Delete</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

  </section>

</body>
</html>
