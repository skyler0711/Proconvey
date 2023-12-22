import { ComponentStory, ComponentMeta } from '@storybook/react'
import ProgressBar from './index'

export default {
  title: 'Components/ProgressBar',
  component: ProgressBar,
} as ComponentMeta<typeof ProgressBar>

export const Expanded: ComponentStory<typeof ProgressBar> = () => {
  return (
    <ProgressBar>
      <ProgressBar.Item progress={100} text="Getting Started" />
      <ProgressBar.Item progress={50} text="Step 2" />
      <ProgressBar.Item progress={0} text="Step 2" />
      <ProgressBar.Item progress={0} text="Step 2" />
    </ProgressBar>
  )
}
