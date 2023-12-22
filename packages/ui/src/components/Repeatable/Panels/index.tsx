import { Tab } from '@headlessui/react'

type PropTypes = {
  children: React.ReactNode
}

const Panels = ({ children }: PropTypes) => {
  return (
    <Tab.Panels className="mt-[3.125rem]">
      {children}
    </Tab.Panels>
  )
}

export default Panels
