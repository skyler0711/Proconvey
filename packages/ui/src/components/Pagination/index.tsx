import classNames from 'classnames'
import { ChevronLeftIcon, ChevronRightIcon } from '../../icons'
import { ReactNode } from 'react'

type PropTypes = {
  total: number
  currentPage: number
  onPageSelected?: (page: number) => void
}

type ButtonPropTypes = {
  children: ReactNode
  selected?: boolean
  onClick?: () => void
}

const PaginationButton = ({ children, selected = false, onClick }: ButtonPropTypes) => {
  return (
    <button onClick={onClick} className={classNames(
      'w-[2.5rem] h-[2.5rem] border rounded-[0.625rem] flex items-center justify-center',
      {
        'bg-white border-primary-ring': !selected,
        'bg-primary text-white border-primary': selected,
      },
    )}>
      {children}
    </button>
  )
}

const Pagination = ({ total, currentPage, onPageSelected = () => {} }: PropTypes) => {
  let pages: number[] = []

  if (total <= 6) {
    pages = Array.from({ length: total }).map((_, page) => page + 1)
  } else {
    // TODO: Handle the "..." button
    pages = Array.from({ length: total }).map((_, page) => page + 1)
  }

  if (pages.length === 0) {
    return null
  }

  return (
    <div className="flex gap-[0.625rem]">
      <PaginationButton onClick={currentPage === 1 ? undefined : () => onPageSelected(currentPage - 1)}>
        <ChevronLeftIcon />
      </PaginationButton>
      {
        pages.map((page) => (
          <PaginationButton key={page} selected={page === currentPage} onClick={() => onPageSelected(page)}>
            {page}
          </PaginationButton>
        ))
      }
      <PaginationButton onClick={currentPage === total ? undefined : () => onPageSelected(currentPage + 1)}>
        <ChevronRightIcon />
      </PaginationButton>
    </div>
  )
}

export default Pagination
