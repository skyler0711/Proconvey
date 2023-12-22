import { ComponentStory, ComponentMeta } from '@storybook/react'
import PercentageCircle from './index'

export default {
  title: 'Components/PercentageCircle',
  component: PercentageCircle,
} as ComponentMeta<typeof PercentageCircle>

const Template: ComponentStory<typeof PercentageCircle> = (args) => <PercentageCircle {...args} />

export const HalfProgress: ComponentStory<typeof PercentageCircle> = Template.bind({})

HalfProgress.args = {
  percentage: 50,
}

export const FullCircle: ComponentStory<typeof PercentageCircle> = Template.bind({})

FullCircle.args = {
  percentage: 100,
}
