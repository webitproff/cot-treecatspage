<!-- BEGIN: MAIN -->
<ul<!-- IF {LEVEL} == 0 --> class="nav flex-column"<!-- ELSE --> class="nav flex-column ps-3"<!-- ENDIF -->>
	<!-- IF {LEVEL} == 0 -->
	<li class="nav-item">
		<div class="d-flex align-items-center" title="{PHP.L.All}">
			<i class="fa-regular fa-folder me-2"></i>
			<span>{PHP.L.All}</span>
			<span class="ms-auto">({TOTAL_COUNT})</span>
		</div>
	</li>
	<hr class="my-2">
	<!-- ENDIF -->
	<!-- BEGIN: CATS -->
	<li class="nav-item">
		<!-- IF {ROW_SUBCAT} -->
		<div class="d-flex align-items-center">
			<!-- Полноценная ссылка на категорию (занимает всё пространство кроме стрелки) -->
			<a href="{ROW_HREF}" class="nav-link flex-grow-1 d-flex align-items-center text-decoration-none text-reset<!-- IF {ROW_SELECTED} --> active<!-- ENDIF -->">
				<i class="fa-regular fa-folder me-2"></i>
				<span>{ROW_TITLE}</span>
				<span class="ms-auto">({ROW_COUNT})</span>
			</a>
			
			<!-- Стрелка — ТОЛЬКО toggle, НЕ ссылка -->
			<button type="button" class="btn btn-link text-reset px-2 py-0"
            data-bs-toggle="collapse"
            data-bs-target="#collapse-{ROW_ID}"
            aria-expanded="false"
            aria-controls="collapse-{ROW_ID}">
				<i class="fa fa-angle-down text-bg-info p-1"></i>
			</button>
		</div>
		
		<div class="collapse" id="collapse-{ROW_ID}">
			{ROW_SUBCAT}
		</div>
		<!-- ELSE -->
		<a href="{ROW_HREF}" class="nav-link d-flex align-items-center<!-- IF {ROW_SELECTED} --> active<!-- ENDIF -->">
			<i class="fa-solid fa-file me-2"></i>
			<span>{ROW_TITLE}</span>
			<span class="ms-auto">({ROW_COUNT})</span>
		</a>
		<!-- ENDIF -->
	</li>
	<!-- END: CATS -->
	
</ul>
<!-- END: MAIN -->

