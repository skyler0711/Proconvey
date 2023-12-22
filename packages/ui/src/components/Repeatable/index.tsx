import { Tab as HeadlessTab } from '@headlessui/react'
import Panel from './Panel'
import Panels from './Panels'
import Tab from './Tab'
import Tabs from './Tabs'

type PropTypes = {
  children: React.ReactNode
  defaultIndex?: number
}

const Repeatable = ({ children, defaultIndex }: PropTypes) => {
  return (
    <HeadlessTab.Group defaultIndex={defaultIndex ?? 0}>
      {children}
    </HeadlessTab.Group>
  )
}

Repeatable.Tabs = Tabs
Repeatable.Tab = Tab
Repeatable.Panels = Panels
Repeatable.Panel = Panel

export default Repeatable
