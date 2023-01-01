set ts=4 sts=4 sw=4 et

" Open the src folder in NerdTree by default
au User NERDTreeInit global/src/normal o

" Press F6 to run tests.
map <F6> :!make lint-phpstan<CR>
