import { Fragment } from 'react'
import { Tab as HeadlessTab } from '@headlessui/react'
import classNames from 'classnames'
import { WarningIcon } from '../../../icons'

type PropTypes = {
  children: React.ReactNode
  hasError?: boolean
}

const Tab = ({ children, hasError = false }: PropTypes) => {
  return (
    <HeadlessTab as={Fragment}>
      {({ selected }) => (
        <button className={classNames('transition-colors duration-100 flex flex-1 gap-1.5 justify-center rounded-lg px-[1.875rem] py-[1.1875rem] text-lg font-bold leading-[1.375rem] focus-visible:ring-2.5 min-w-[9.375rem]', {
          'bg-primary text-white': selected && !hasError,
          'text-primary text-opacity-50': !selected && !hasError,

          'bg-danger text-white': selected && hasError,
          'text-danger text-opacity-50': !selected && hasError,

          'focus-visible:bg-primary-focus focus-visible:ring-primary-ring': !hasError,
          'focus-visible:ring-danger focus-visible:ring-opacity-30': hasError,
        })}>
          {hasError &&
            <WarningIcon className="w-5 h-5 my-auto right-10" />
          }

          {children}
        </button>
      )}
    </HeadlessTab>
  )
}

export default Tab
