

      <div class="offcanvas offcanvas-start" tabindex="-1" id="treecatspageLeftOffcanvas" aria-labelledby="treecatspageLeftOffcanvasLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="treecatspageLeftOffcanvasLabel">
            <span class="me-2">
                    <i class="fa-regular fa-newspaper me-2"></i>
                  </span>{PHP.L.2wd_Publications}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
			{PHP|cot_build_structure_page_tree('', '')}
        </div>
      </div>