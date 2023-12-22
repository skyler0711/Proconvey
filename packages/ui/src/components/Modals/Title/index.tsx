import { Dialog } from '@headlessui/react'
import { H2 } from '../../Headers'

type PropTypes = {
  children: React.ReactNode
}

const Title = ({ children }: PropTypes) => {
  return (
    <Dialog.Title as="div" className="w-[calc(100%-28px)] flex items-center justify-between gap-3 mb-5">
      <H2>{children}</H2>
    </Dialog.Title>
  )
}

export default Title
