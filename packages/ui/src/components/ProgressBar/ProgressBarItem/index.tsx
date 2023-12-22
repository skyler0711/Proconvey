import PercentageCircle from '../../PercentageCircle'

type PropTypes = {
  progress: number
  text: string
}

const SidebarItem = ({ progress, text }: PropTypes) => {

  return (
    <div className="min-w-[96px] max-w-[6rem] text-center">
      <div className="max-w-min mx-auto">
        <PercentageCircle strokeWidth={8} percentage={progress} height={78} width={78} fontSize={14} innerCircleClassNames="h-[calc(100%-35.89%)] w-[calc(100%-35.89%)]" />
      </div>
      <p className="text-body text-base leading-[1.125rem] font-medium group-focus:underline mt-[1.125rem]">{text}</p>
    </div>
  )
}

export default SidebarItem
