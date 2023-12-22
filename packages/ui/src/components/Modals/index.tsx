import { Fragment } from 'react'
import { Dialog, Transition } from '@headlessui/react'
import Title from './Title'
import Content from './Content'
import ContentTitle from './ContentTitle'
import Footer from './Footer'
import Button from '../../components/Button'
import { CrossIcon } from '../../icons'
import classNames from 'classnames'

type PropTypes = {
  children: React.ReactNode
  isOpen: boolean
  onClose: () => void
  onAnimationEnd?: () => void
  size?: 'medium' | 'large'
  fullWidth?: boolean
}

const Modal = ({ children, isOpen = false, onClose, onAnimationEnd, size = 'medium', fullWidth = false }: PropTypes) => {
  return (
    <Transition appear show={isOpen} as={Fragment}>
      <Dialog as="div" className="relative z-10" onClose={onClose} onAnimationEnd={onAnimationEnd}>
        <Transition.Child
          as={Fragment}
          enter="ease-out duration-300"
          enterFrom="opacity-0"
          enterTo="opacity-100"
          leave="ease-in duration-200"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
        >
          <div className="fixed inset-0 bg-opacity-50 bg-body" />
        </Transition.Child>

        <div className="fixed inset-0 overflow-y-auto">
          <div className="flex items-center justify-center min-h-full p-4">
            <Transition.Child
              as={Fragment}
              enter="ease-out duration-300"
              enterFrom="opacity-0 scale-95"
              enterTo="opacity-100 scale-100"
              leave="ease-in duration-200"
              leaveFrom="opacity-100 scale-100"
              leaveTo="opacity-0 scale-95"
            >
              <Dialog.Panel className={classNames(
                'relative md flex flex-col md:mb-auto min-h-full md:min-h-[225px] rounded-[0.625rem] p-5 mx-auto w-full max-h-[95vh] overflow-y-auto bg-white',

                {
                  'max-w-none': fullWidth,
                  'max-w-[50.1875rem]': !fullWidth,
                },
                {
                  'md:max-w-[50.1875rem]': size === 'medium' && !fullWidth,
                  'md:max-w-[70%]': size === 'large' && !fullWidth,
                },
              )}>
                <div className="absolute top-[25px] right-[25px]">
                  <Button variant="link" onClick={onClose}>
                    <CrossIcon className="w-3 h-3 text-body text-opacity-60" />
                  </Button>
                </div>
                {children}
              </Dialog.Panel>
            </Transition.Child>
          </div>
        </div>
      </Dialog>
    </Transition>
  )
}

Modal.Title = Title
Modal.Content = Content
Modal.Footer = Footer
Modal.ContentTitle = ContentTitle

export default Modal
