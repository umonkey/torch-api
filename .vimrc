set ts=4 sts=4 sw=4 et

" Open the src folder in NerdTree by default
au User NERDTreeInit global/src/normal o

" Press F6 to run tests.
" Navigate with :copen, :cclose, :cn, :cp
map <F6> :call LintStan()<CR>

function! LintStan()
  setlocal efm=%f:%l:%m
  wall
  cexpr system('make lint')
  copen
endfunction
