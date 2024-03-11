<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="./libs/img/favicon.ico" type="image/x-icon" />
  <!-- bootstrap -->
  <link rel="stylesheet" href="vendors/bootstrap/css/bootstrap.min.css" />
  <!-- fontAwesome -->
  <link rel="stylesheet" href="vendors/fontAwesome/css/all.min.css" />
  <link rel="stylesheet" href="vendors/fontAwesome/css/fontawesome.min.css" />

  <!-- Toastify CSS-->
  <link rel="stylesheet" href="vendors/toastify/css/toastify.css" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="libs/css/styles.css" />


  <title>Company Directory</title>
</head>

<body>
  <section>
    <div class="appHeader">
      <div class="row">
        <!-- Filter Modal -->
        <div id="filterModal" class="modal fade" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow">
              <div class="modal-header bg-primary bg-gradient text-white">
                <h5 class="modal-title">Filter</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="filterPersonnelForm">
                  <div class="form-floating">
                    <select class="form-select mb-3" id="filterDepartment" placeholder="Department">
                    </select>
                    <label for="filterDepartment">Department</label>
                  </div>
                  <div class="form-floating">
                    <select class="form-select" id="filterLocation" placeholder="Department">
                    </select>
                    <label for="filterLocation">Location</label>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="submit" form="filterPersonnelForm" class="btn btn-outline-primary btn-sm myBtn">
                  SAVE
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm myBtn" data-bs-dismiss="modal">
                  CANCEL
                </button>
              </div>
            </div>
          </div>
        </div>
        <!--------- Search Input Area ------>
        <div class="col">
          <input id="searchInp" class="form-control mb-3" placeholder="search" />
        </div>

        <!--------- Buttons Tab Area ------>
        <div class="col-6 text-end me-2">
          <div class="btn-group" role="group" aria-label="buttons">
            <button id="refreshBtn" type="button" class="btn btn-primary">
              <i class="fa-solid fa-refresh fa-fw"></i>
            </button>
            <button id="filterBtn" type="button" class="btn btn-primary">
              <i class="fa-solid fa-filter fa-fw"></i>
            </button>
            <button id="addBtn" type="button" class="btn btn-primary">
              <i class="fa-solid fa-plus fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="personnelBtn" data-bs-toggle="tab" data-bs-target="#personnel-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">
            <i class="fa-solid fa-person fa-lg fa-fw"></i><span class="d-none d-sm-block">Personnel</span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="departmentsBtn" data-bs-toggle="tab" data-bs-target="#departments-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
            <i class="fa-solid fa-building fa-lg fa-fw"></i><span class="d-none d-sm-block">Departments</span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="locationsBtn" data-bs-toggle="tab" data-bs-target="#locations-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">
            <i class="fa-solid fa-map-location-dot fa-lg fa-fw"></i><span class="d-none d-sm-block">Locations</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- DATA TABLES  -->
    <div class="tab-content bg-white">
      <div class="tab-pane show active" id="personnel-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
        <table class="table table-hover">
          <tbody id="personnelTable"></tbody>
        </table>
      </div>
      <div class="tab-pane" id="departments-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
        <table class="table table-hover">
          <tbody id="departmentTable"></tbody>
        </table>
      </div>
      <div class="tab-pane" id="locations-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
        <table class="table table-hover">
          <tbody id="locationTable"></tbody>
        </table>
      </div>
    </div>

    <!-- FOOTER  -->
    <footer class="border-top text-center fw-bold">
      <p class="fw-light my-3">Company Directory version 1.0</p>
    </footer>
  </section>

  <!-- ADD PERSONNEL MODAL -->
  <div id="addPersonnelModal" class="modal fade" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-success bg-gradient text-white">
          <h5 class="modal-title">Add Personnel</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addPersonnelForm">
            <input type="hidden" id="addPersonnelEmployeeID" />
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="addPersonnelFirstName" placeholder="First name" required />
              <label for="addPersonnelFirstName">First name</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="addPersonnelLastName" placeholder="Last name" required />
              <label for="addPersonnelLastName">Last name</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="addPersonnelJobTitle" placeholder="Job title" />
              <label for="addPersonnelJobTitle">Job Title</label>
            </div>
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="addPersonnelEmailAddress" placeholder="Email address" required />
              <label for="addPersonnelEmailAddress">Email Address</label>
            </div>
            <div class="form-floating">
              <select class="form-select" id="addPersonnelDepartment" placeholder="Department"></select>
              <label for="addPersonnelDepartment">Department</label>
            </div>

          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" form="addPersonnelForm" class="btn btn-outline-success btn-sm myBtn">
            SAVE
          </button>
          <button type="button" class="btn btn-outline-success btn-sm myBtn" data-bs-dismiss="modal">
            CANCEL
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- END ADD PERSONNEL MODAL -->

  <!-- EDIT PERSONNEL MODAL -->
  <div id="editPersonnelModal" class="modal fade" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-primary bg-gradient text-white">
          <h5 class="modal-title">Edit employee</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editPersonnelForm">
            <input type="hidden" id="editPersonnelEmployeeID" />
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="editPersonnelFirstName" placeholder="First name" required />
              <label for="editPersonnelFirstName">First name</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="editPersonnelLastName" placeholder="Last name" required />
              <label for="editPersonnelLastName">Last name</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="editPersonnelJobTitle" placeholder="Job title" />
              <label for="editPersonnelJobTitle">Job Title</label>
            </div>
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="editPersonnelEmailAddress" placeholder="Email address" required />
              <label for="editPersonnelEmailAddress">Email Address</label>
            </div>
            <div class="form-floating">
              <select class="form-select" id="editPersonnelDepartment" placeholder="Department"></select>
              <label for="editPersonnelDepartment">Department</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" form="editPersonnelForm" class="btn btn-outline-primary btn-sm myBtn">
            SAVE
          </button>
          <button type="button" class="btn btn-outline-primary btn-sm myBtn" data-bs-dismiss="modal">
            CANCEL
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- END EDIT PERSONNEL MODAL -->

  <!-- DELETE PERSONNEL MODAL -->
  <div id="deletePersonnelModal" class="modal fade">
    <div class="modal-dialog modal-md d-sm:modal-sm modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-danger bg-gradient text-white">
          <h5 class="modal-title">Delete employee</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="deletePersonnelForm">
            <input type="hidden" id="deletePersonnelEmployeeID">
            <h5>Are you sure you want to delete <span style="font-weight: 700;" id="employeeName"></span>'s profile?</h5>
            <h5>All data concerning this employee will be lost.</h5>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" form="deletePersonnelForm" class="btn btn-outline-danger btn-sm myBtn">YES</button>
          <button type="button" class="btn btn-outline-primary btn-sm myBtn" data-bs-dismiss="modal">CANCEL</button>
        </div>
      </div>
    </div>
  </div>
  <!-- END DELETE PERSONNEL MODAL -->

  <!--------------------------------- DEPARTMENT ------------------------------------>

  <!--ADD DEPARTMENT MODAL -->
  <div id="addDepartmentModal" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-success bg-gradient text-white">
          <h5 class="modal-title">Add department</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addDepartmentForm">
            <input type="hidden" id="addDepartmentID">
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="addDepartmentName" placeholder="Name" required>
              <label for="addDepartmentName">Name</label>
            </div>
            <div class="form-floating">
              <select class="form-select" id="addDepartmentLocation" placeholder="Location">
              </select>
              <label for="addDepartmentLocation">Location</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" form="addDepartmentForm" class="btn btn-outline-success btn-sm myBtn">SAVE</button>
          <button type="button" class="btn btn-outline-success btn-sm myBtn" data-bs-dismiss="modal">CANCEL</button>
        </div>
      </div>
    </div>
  </div>
  <!--END ADD DEPARTMENT MODAL-->

  <!-- EDIT DEPARTMENT MODAL -->
  <div id="editDepartmentModal" class="modal fade" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-primary bg-gradient text-white">
          <h5 class="modal-title">Edit Department</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editDepartmentForm">
            <input type="hidden" id="editDepartmentID" />
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="editDepartmentName" placeholder="First name" required />
              <label for="editDepartmentName">name</label>
            </div>
            <div class="form-floating">
              <select class="form-select" id="editDepartmentLocation" placeholder="location"></select>
              <label for="editDepartmentLocation">Location</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" form="editDepartmentForm" class="btn btn-outline-primary btn-sm myBtn">
            SAVE
          </button>
          <button type="button" class="btn btn-outline-primary btn-sm myBtn" data-bs-dismiss="modal">
            CANCEL
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- END EDIT DEPARTMENT MODAL -->

  <!-- DELETE DEPARTMENT MODAL -->
  <div id="deleteDepartmentModal" class="modal fade">
    <div class="modal-dialog modal-md d-sm:modal-sm modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-danger bg-gradient text-white">
          <h5 class="modal-title">Delete Department</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="deleteDepartmentForm">
            <input type="hidden" id="deleteDepartmentID">
            <div id="filledDeptMessage">
              <h5 class="">You can't delete <span style="font-weight: 700;" id="departmentName"></span>
                because it has <span style="font-weight: 700;" id="employeeCount"></span> personnels attached to it</h5>
            </div>
            <div id="emptyDeptMessage">
              <h5 class="">You sure you want to delete <span style="font-weight: 700;" id="emptyDepartmentName"></span>?
                Deleted Department can't be recovered</h5>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <div id="filledDepartment">
            <button type="submit" class="btn btn-outline-danger btn-sm myBtn" data-bs-dismiss="modal" aria-label="Close">OK</button>
          </div>
          <div id="emptyDepartment">
            <button type="submit" form="deleteDepartmentForm" class="btn btn-outline-danger btn-sm myBtn">YES</button>
            <button type="button" class="btn btn-outline-primary btn-sm myBtn" data-bs-dismiss="modal">CANCEL</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END DELETE DEPARTMENT MODAL -->


  <!--------------------------------- LOCATION ------------------------------------>

  <!-- ADD LOCATION MODAL -->
  <div id="addLocationModal" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-success bg-gradient text-white">
          <h5 class="modal-title">Add Location</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addLocationForm">
            <input type="hidden" id="addLocationID">
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="addLocationName" placeholder="Name" required>
              <label for="addLocationName">Name</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" form="addLocationForm" class="btn btn-outline-success btn-sm myBtn">SAVE</button>
          <button type="button" class="btn btn-outline-success btn-sm myBtn" data-bs-dismiss="modal">CANCEL</button>
        </div>
      </div>
    </div>
  </div>
  <!--END ADD LOCATION MODAL-->

  <!-- EDIT LOCATION MODAL -->
  <div id="editLocationModal" class="modal fade" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-primary bg-gradient text-white">
          <h5 class="modal-title">Edit Location</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editLocationForm">
            <input type="hidden" id="editLocationID" />
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="editLocationName" placeholder="First name" required />
              <label for="editLocationName">name</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" form="editLocationForm" class="btn btn-outline-primary btn-sm myBtn">
            SAVE
          </button>
          <button type="button" class="btn btn-outline-primary btn-sm myBtn" data-bs-dismiss="modal">
            CANCEL
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- END EDIT LOCATION MODAL -->

  <!-- DELETE LOCATION MODAL -->
  <div id="deleteLocationModal" class="modal fade">
    <div class="modal-dialog modal-md d-sm:modal-sm modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content shadow">
        <div class="modal-header bg-danger bg-gradient text-white">
          <h5 class="modal-title">Delete Location</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="deleteLocationForm">
            <input type="hidden" id="deleteLocationID">
            <div id="filledLocationMessage">
              <h5 class="">You can't delete <span style="font-weight: 700;" id="locationName"></span>
                because it has <span style="font-weight: 700;" id="locationCount"></span> departments attached to it</h5>
            </div>
            <div id="emptyLocationMessage">
              <h5 class="">You sure you want to delete <span style="font-weight: 700;" id="emptyLocationName"></span>?
                Deleted Locations can't be recovered</h5>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <div id="filledLocation">
            <button type="submit" class="btn btn-outline-danger btn-sm myBtn" data-bs-dismiss="modal" aria-label="Close">OK</button>
          </div>
          <div id="emptyLocation">
            <button type="submit" form="deleteLocationForm" class="btn btn-outline-danger btn-sm myBtn">YES</button>
            <button type="button" class="btn btn-outline-primary btn-sm myBtn" data-bs-dismiss="modal">CANCEL</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END DELETE LOCATION MODAL -->

  <!-- all the JavaScript goes here -->
  <!-- Bootstrap JavaScript -->
  <script type="text/javascript" src="vendors/bootstrap/js/bootstrap.min.js"></script>

  <!-- Toastify JavaScript -->
  <script src="vendors/toastify/js/toastify.js"></script>

  <script type="text/javascript" src="vendors/jquery/jquery-3.7.1.min.js"></script>
  <script type="text/javascript" src="libs/js/script.js" defer></script>

</body>

</html>