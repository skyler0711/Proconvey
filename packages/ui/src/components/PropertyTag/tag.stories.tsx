import { ComponentStory, ComponentMeta } from '@storybook/react'
import PropertyTag from './index'

export default {
  title: 'Components/PropertyTag',
  component: PropertyTag,
} as ComponentMeta<typeof PropertyTag>

const Template: ComponentStory<typeof PropertyTag> = (args) => <PropertyTag {...args} />

export const Default: ComponentStory<typeof PropertyTag> = Template.bind({})
Default.args = {
  children: 'Default',
}

export const Sale: ComponentStory<typeof PropertyTag> = Template.bind({})
Sale.args = {
  type: 'sale',
  children: 'Sale',
}

export const Purchase: ComponentStory<typeof PropertyTag> = Template.bind({})
Purchase.args = {
  type: 'purchase',
  children: 'Purchase',
}

export const Remortgage: ComponentStory<typeof PropertyTag> = Template.bind({})
Remortgage.args = {
  type: 'remortgage',
  children: 'Remortgage',
}
