import { Tab } from '@headlessui/react'

type PropTypes = {
  children: React.ReactNode
}

const Tabs = ({ children }: PropTypes) => {
  return (
    <Tab.List className="flex flex-col md:flex-row flex-wrap p-[0.3125rem] bg-selago rounded-[10px]">
      {children}
    </Tab.List>
  )
}

export default Tabs
