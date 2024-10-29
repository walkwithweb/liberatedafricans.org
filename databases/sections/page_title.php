<div class="row justify-content-center">
    <div id="pageTitleContainer" class="col-11 justify-content-start">
    <div class="row">
        <div id="leftTitle" class="col-xxl-5 d-flex">
            <h1 class="pageTitle">Courts & Cases</h1>
        </div>
        <div class="col-xxl-7 d-flex btnContainer pr-2">
            <ul class="nav">
                <li class="nav-item">
                    <a href="cases-departures.php" class="btn <?php echo $current_page == '1' ? 'activeNav':'' ?>">DEPARTURES</a>
                </li>
                <li class="nav-item">
                    <a href="cases-blockades.php" class="btn <?php echo $current_page == '2' ? 'activeNav':'' ?>">BLOCKADES</a>
                </li>
                <li class="nav-item">
                    <a href="cases.php" class="btn <?php echo $current_page == '3' ? 'activeNav':'' ?>">LIBERATED AFRICANS</a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="pageTitleBorder">
    <?php if(isset($_GET['msg']) && $_GET['msg']=='0'):?>
        <div class="alert alert-warning alert-dismissible alert-noresults fade show" role="alert">
        <strong>No results matching your query were found...</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif;?>
    </div>
</div>