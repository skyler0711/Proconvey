import { ComponentStory, ComponentMeta } from '@storybook/react'
import ProgressSidebar from './index'

export default {
  title: 'Components/ProgressSidebar',
  component: ProgressSidebar,
} as ComponentMeta<typeof ProgressSidebar>

export const Expanded: ComponentStory<typeof ProgressSidebar> = () => {
  return (
    <div className="h-screen flex">
      <ProgressSidebar>
        <ProgressSidebar.Item active={false} progress={100} text="Getting Started">
          <ProgressSidebar.SubItem active={false} completed={true}>Question 1 is a really long question that causes overflow</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={false} completed={true}>Question 2</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={false} completed={true}>Question 3</ProgressSidebar.SubItem>
        </ProgressSidebar.Item>
        <ProgressSidebar.Item active={true} progress={50} text="Step 2">
          <ProgressSidebar.SubItem active={false} completed={true}>Question 1</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={true} completed={false}>Question 2</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={false} completed={false}>Question 3</ProgressSidebar.SubItem>
        </ProgressSidebar.Item>
        <ProgressSidebar.Item active={false} progress={0} text="Step 2">
          <ProgressSidebar.SubItem active={false} completed={true}>Question 1</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={true} completed={false}>Question 2</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={false} completed={false}>Question 3</ProgressSidebar.SubItem>
        </ProgressSidebar.Item>
        <ProgressSidebar.Item active={false} progress={0} text="Step 2">
          <ProgressSidebar.SubItem active={false} completed={true}>Question 1</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={true} completed={false}>Question 2</ProgressSidebar.SubItem>
          <ProgressSidebar.SubItem active={false} completed={false}>Question 3</ProgressSidebar.SubItem>
        </ProgressSidebar.Item>
      </ProgressSidebar>
    </div>
  )
}
