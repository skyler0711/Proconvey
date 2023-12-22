import { Tab } from '@headlessui/react'

type PropTypes = {
  children: React.ReactNode
}

const Panel = ({ children }: PropTypes) => {
  return (
    <Tab.Panel>
      {children}
    </Tab.Panel>
  )
}

export default Panel
