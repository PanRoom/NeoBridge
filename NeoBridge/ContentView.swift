import SwiftUI

struct ContentView: View {
    // MARK: - 状態変数
    @State private var fullName: String = "" // 名前を記憶する変数
    @State private var affiliation: String = "" // 所属を記憶する変数
    @State private var birthDate: Date = Date() // 生年月日を記憶する変数
    @State private var selectedGender: Gender = .male // 性別を記憶する変数

    // 性別選択肢の定義
    enum Gender: String, CaseIterable, Identifiable {
        case male = "男性"
        case female = "女性"
        
        var id: String { self.rawValue }
    }

    // MARK: - Body
    var body: some View {
        NavigationView {
            Form {
                // MARK: - 基本情報セクション
                Section(header: Text("基本情報")) {
                    // 名前入力フィールド
                    TextField("名前", text: $fullName)
                    
                    // 所属入力フィールド
                    TextField("所属 (大学名、会社名など)", text: $affiliation)
                }
                
                // MARK: - 詳細情報セクション
                Section(header: Text("詳細情報")) {
                    // 生年月日選択カレンダー
                    DatePicker(
                        "生年月日",
                        selection: $birthDate,
                        in: ...Date(), // 未来の日付を選択できないように制限
                        displayedComponents: .date
                    )
                    
                    // 性別選択ピッカー
                    Picker("性別", selection: $selectedGender) {
                        ForEach(Gender.allCases) { gender in
                            Text(gender.rawValue).tag(gender)
                        }
                    }
                }
                
                // MARK: - 入力内容の確認セクション (開発用)
                Section(header: Text("入力内容の確認")) {
                    Text("名前: \(fullName)")
                    Text("所属: \(affiliation)")
                    Text("生年月日: \(birthDate, formatter: dateFormatter)")
                    Text("性別: \(selectedGender.rawValue)")
                }
            }
            .navigationTitle("プロフィール作成")
        }
    }
    
    // MARK: - Helper
    // 日付を「yyyy年M月d日」形式に変換するフォーマッター
    private var dateFormatter: DateFormatter {
        let formatter = DateFormatter()
        formatter.dateStyle = .long
        formatter.locale = Locale(identifier: "ja_JP")
        return formatter
    }
}

// MARK: - Preview
#Preview {
    ContentView()
}
