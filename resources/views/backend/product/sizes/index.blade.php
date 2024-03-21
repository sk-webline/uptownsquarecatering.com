@extends('backend.layouts.app')

@section('content')

<div class="sk-titlebar text-left mt-2 mb-3">
	<div class="align-items-center">
			<h1 class="h3">{{translate('All Sizes')}}</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-7">
		<div class="card">
		    <div class="card-header row gutters-5">
          <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6">{{ translate('Sizes') }}</h5>
          </div>
		    </div>
		    <div class="card-body">
          <form action="{{ route('sizes.update_order') }}" method="POST">
            @csrf
            <div class="mar-all text-right">
              <button type="submit" name="button" class="btn btn-primary">{{ translate('Save') }}</button>
            </div>
		        <table class="table sk-table mb-0 draggable-table" id="sortable-table">
		            <thead>
		                <tr>
		                    <th>#</th>
		                    <th>{{translate('Name')}}</th>
		                    <th>{{translate('BTMS Code')}}</th>
		                </tr>
		            </thead>
		            <tbody>
		                @foreach($sizes as $key => $size)
		                    <tr>
		                        <td>{{ $key+1 }} <input type="hidden" name="sort_sizes[]" value="{{ $size->id }}"></td>
		                        <td>{{ $size->btms_size_name }}</td>
		                        <td>{{ $size->btms_size_code }}</td>
		                    </tr>
		                @endforeach
		            </tbody>
		        </table>
            <div class="mar-all text-right">
              <button type="submit" name="button" class="btn btn-primary">{{ translate('Save') }}</button>
            </div>
          </form>
		    </div>
		</div>
	</div>
</div>

@endsection

@section('script')
  <script type="text/javascript">
    (function() {
      "use strict";

      const table = document.getElementById('sortable-table');
      const tbody = table.querySelector('tbody');

      var currRow = null,
        dragElem = null,
        mouseDownX = 0,
        mouseDownY = 0,
        mouseX = 0,
        mouseY = 0,
        mouseDrag = false;

      function init() {
        bindMouse();
      }

      function bindMouse() {
        document.addEventListener('mousedown', (event) => {
          if(event.button != 0) return true;

          let target = getTargetRow(event.target);
          if(target) {
            currRow = target;
            addDraggableRow(target);
            currRow.classList.add('is-dragging');


            let coords = getMouseCoords(event);
            mouseDownX = coords.x;
            mouseDownY = coords.y;

            mouseDrag = true;
          }
        });

        document.addEventListener('mousemove', (event) => {
          if(!mouseDrag) return;

          let coords = getMouseCoords(event);
          mouseX = coords.x - mouseDownX;
          mouseY = coords.y - mouseDownY;

          moveRow(mouseX, mouseY);
        });

        document.addEventListener('mouseup', (event) => {
          if(!mouseDrag) return;

          currRow.classList.remove('is-dragging');
          table.removeChild(dragElem);

          dragElem = null;
          mouseDrag = false;
        });
      }


      function swapRow(row, index) {
        let currIndex = Array.from(tbody.children).indexOf(currRow),
          row1 = currIndex > index ? currRow : row,
          row2 = currIndex > index ? row : currRow;

        tbody.insertBefore(row1, row2);
      }

      function moveRow(x, y) {
        dragElem.style.transform = "translate3d(" + x + "px, " + y + "px, 0)";

        let	dPos = dragElem.getBoundingClientRect(),
          currStartY = dPos.y, currEndY = currStartY + dPos.height,
          rows = getRows();

        for(var i = 0; i < rows.length; i++) {
          let rowElem = rows[i],
            rowSize = rowElem.getBoundingClientRect(),
            rowStartY = rowSize.y, rowEndY = rowStartY + rowSize.height;

          if(currRow !== rowElem && isIntersecting(currStartY, currEndY, rowStartY, rowEndY)) {
            if(Math.abs(currStartY - rowStartY) < rowSize.height / 2)
              swapRow(rowElem, i);
          }
        }
      }

      function addDraggableRow(target) {
        dragElem = target.cloneNode(true);
        dragElem.classList.add('draggable-table__drag');
        dragElem.style.height = getStyle(target, 'height');
        dragElem.style.background = getStyle(target, 'backgroundColor');
        for(var i = 0; i < target.children.length; i++) {
          let oldTD = target.children[i],
            newTD = dragElem.children[i];
          newTD.style.width = getStyle(oldTD, 'width');
          newTD.style.height = getStyle(oldTD, 'height');
          newTD.style.padding = getStyle(oldTD, 'padding');
          newTD.style.margin = getStyle(oldTD, 'margin');
        }

        table.appendChild(dragElem);


        let tPos = target.getBoundingClientRect(),
          dPos = dragElem.getBoundingClientRect();
        dragElem.style.bottom = ((dPos.y - tPos.y) - tPos.height) + "px";
        dragElem.style.left = "-1px";

        document.dispatchEvent(new MouseEvent('mousemove',
          { view: window, cancelable: true, bubbles: true }
        ));
      }







      function getRows() {
        return table.querySelectorAll('tbody tr');
      }

      function getTargetRow(target) {
        let elemName = target.tagName.toLowerCase();

        if(elemName == 'tr') return target;
        if(elemName == 'td') return target.closest('tr');
      }

      function getMouseCoords(event) {
        return {
          x: event.clientX,
          y: event.clientY
        };
      }

      function getStyle(target, styleName) {
        let compStyle = getComputedStyle(target),
          style = compStyle[styleName];

        return style ? style : null;
      }

      function isIntersecting(min0, max0, min1, max1) {
        return Math.max(min0, max0) >= Math.min(min1, max1) &&
          Math.min(min0, max0) <= Math.max(min1, max1);
      }



      init();

    })();
  </script>
@endsection
